<?php
/**
 * staff-management plugin for Craft CMS 3.x
 *
 * Craft Staff Management provides an HR solution for payroll and benefits
 *
 * @link      http://percipio.london
 * @copyright Copyright (c) 2021 Percipio
 */

namespace percipiolondon\staff\services;

use Craft;
use craft\base\Component;

use percipiolondon\staff\helpers\Security as SecurityHelper;
use percipiolondon\staff\elements\Employee;
use percipiolondon\staff\records\Employee as EmployeeRecord;
use percipiolondon\staff\jobs\CreateEmployeeJob;
use percipiolondon\staff\records\EmploymentDetails;
use percipiolondon\staff\records\PersonalDetails;
use percipiolondon\staff\Staff;
use percipiolondon\staff\jobs\FetchEmployeesListJob;
use percipiolondon\staff\helpers\Logger;
use yii\base\BaseObject;
use yii\db\Exception;

/**
 * Employees Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    Percipio
 * @package   Staff
 * @since     1.0.0-alpha.1
 */
class Employees extends Component
{
    // Public Methods
    // =========================================================================
    public function fetchEmployeesByEmployer(array $employer)
    {
        $queue = Craft::$app->getQueue();
        $queue->push(new FetchEmployeesListJob([
            'description' => 'Fetch the employees',
            'criteria' => [
                'employer' => $employer
            ]
        ]));

    }

    public function fetchEmployee(array $employee, array $employer)
    {
        $queue = Craft::$app->getQueue();
        $queue->push(new CreateEmployeeJob([
            'description' => 'Save employees',
            'criteria' => [
                'employee' => $employee,
                'employer' => $employer
            ]
        ]));
    }

    public function saveEmployee(array $employee, string $employeeName, array $employer)
    {
        $employeeRecord = EmployeeRecord::findOne(['staffologyId' => $employee['id']]);

        // check if employee doesn't exist
        if (!$employeeRecord) {

            $logger = new Logger();
            $logger->stdout("✓ Save employee " .$employeeName . '...', $logger::RESET);

            $employeeRecord = new Employee();

            $employeeRecord->employerId = $employer['id'];
            $employeeRecord->staffologyId = $employee['id'];
            $employeeRecord->siteId = Craft::$app->getSites()->currentSite->id;
            $employeeRecord->personalDetails = $employee['personalDetails'] ?? null;
            $employeeRecord->employmentDetails = $employee['employmentDetails'] ?? null;
            $employeeRecord->leaveSettings = $employee['leaveSettings'] ?? null;
            $employeeRecord->status = $employee['status'] ?? '';
            $employeeRecord->niNumber = $employee['personalDetails']['niNumber'] ?? null;
            $employeeRecord->userId = null;
            $employeeRecord->isDirector = $this->isDirector ?? false;

            // save new employee
            $elementsService = Craft::$app->getElements();
            $success = $elementsService->saveElement($employeeRecord);

            if($success){
                $logger->stdout(" done" . PHP_EOL, $logger::FG_GREEN);
            }else{
                $logger->stdout(" failed" . PHP_EOL, $logger::FG_RED);

                $errors = "";

                foreach($employeeRecord->errors as $err) {
                    $errors .= implode(',', $err);
                }

                $logger->stdout($errors . PHP_EOL, $logger::FG_RED);
                Craft::error($employeeRecord->errors, __METHOD__);
            }
        }
    }

    public function saveEmploymentdetails(array $employmentDetails, int $employmentDetailsId = null): EmploymentDetails
    {
        if($employmentDetailsId) {
            $record = EmploymentDetails::findOne($employmentDetailsId);

            if (!$record) {
                throw new Exception('Invalid personal details ID: ' . $employmentDetailsId);
            }

        } else {
            $record = new EmploymentDetails();
        }
        
        $record->cisSubContractor = $employmentDetails['cisSubCopntractor'] ?? null;
        $record->payrollCode = SecurityHelper::encrypt($employmentDetails['cisSubCopntractor'] ?? '');
        $record->jobTitle = SecurityHelper::encrypt($employmentDetails['jobTitle'] ?? '');
        $record->onHold = $employmentDetails['onHold'] ?? null;
        $record->onFurlough = $employmentDetails['onFurlough'] ?? null;
        $record->furloughStart = $employmentDetails['furloughStart'] ?? null;
        $record->furloughEnd = $employmentDetails['furloughEnd'] ?? null;
        $record->furloughCalculationBasis = $employmentDetails['furloughCalculationBasis'] ?? null;
        $record->furloughCalculationBasisAmount = SecurityHelper::encrypt($employmentDetails['furloughCalculationBasisAmount'] ?? '');
        $record->partialFurlough = $employmentDetails['partialFurlough'] ?? null;
        $record->furloughHoursNormallyWorked = $employmentDetails['furloughHoursNormallyWorked'] ?? null;
        $record->furloughHoursOnFurlough = $employmentDetails['furloughHoursOnFurlough'] ?? null;
        $record->isApprentice = $employmentDetails['isApprentice'] ?? null;
        $record->apprenticeshipStartDate = $employmentDetails['apprenticeshipStartDate'] ?? null;
        $record->apprenticeshipEndDate = $employmentDetails['apprenticeshipEndDate'] ?? null;
        $record->workingPattern = $employmentDetails['workingPattern'] ?? null;
        $record->forcePreviousPayrollCode = SecurityHelper::encrypt($employmentDetails['forcePreviousPayrollCode'] ?? '');

        $record->save();

        return $record;
    }
    
    public function savePersonalDetails(array $personalDetails, int $personalDetailsId = null): PersonalDetails
    {
        if($personalDetailsId) {
            $record = PersonalDetails::findOne($personalDetailsId);

            if (!$record) {
                throw new Exception('Invalid personal details ID: ' . $personalDetailsId);
            }

            //foreign keys
            $addressId = $record->addressId;

        } else {
            $record = new PersonalDetails();

            //foreign keys
            $addressId = null;
        }

        //foreign keys
        $address = Staff::$plugin->addresses->saveAddress($personalDetails['address'] ?? [], $addressId);

        $record->addressId = $address->id;

        $record->maritalStatus = $personalDetails['maritalStatus'] ?? 'Unknown';
        $record->title = SecurityHelper::encrypt($personalDetails['title'] ?? '');
        $record->firstName = SecurityHelper::encrypt($personalDetails['firstName'] ?? '');
        $record->middleName = SecurityHelper::encrypt($personalDetails['middleName'] ?? '');
        $record->lastName = SecurityHelper::encrypt($personalDetails['lastName'] ?? '');
        $record->email = SecurityHelper::encrypt($personalDetails['email'] ?? '');
        $record->emailPayslip = $personalDetails['emailPayslip'] ?? null;
        $record->passwordProtectPayslip = $personalDetails['passwordProtectPayslip'] ?? null;
        $record->pdfPassword = SecurityHelper::encrypt($personalDetails['pdfPassword'] ?? '');
        $record->telephone = SecurityHelper::encrypt($personalDetails['telephone'] ?? '');
        $record->mobile = SecurityHelper::encrypt($personalDetails['mobile'] ?? '');
        $record->dateOfBirth = $personalDetails['dateOfBirth'] ?? null;
        $record->statePensionAge = $personalDetails['statePensionAge'] ?? null;
        $record->gender = $personalDetails['gender'] ?? null;
        $record->niNumber = SecurityHelper::encrypt($personalDetails['niNumber'] ?? '');
        $record->passportNumber = SecurityHelper::encrypt($personalDetails['passportNumber'] ?? '');

        $record->save();

        return $record;
    }

//    /**
//     * This function can literally be anything you want, and you can have as many service
//     * functions as you want
//     *
//     * From any other plugin file, call it like this:
//     *
//     *     Staff::$plugin->employees->exampleService()
//     *
//     * @return mixed
//     */
//    public function fetch2()
//    {
//        $apiKey = \Craft::parseEnv(Staff::$plugin->getSettings()->apiKeyStaffology);
//
//        if ($apiKey) {
//
//            $credentials = base64_encode('staff:' . $apiKey);
//            $headers = [
//                'headers' => [
//                    'Authorization' => 'Basic ' . $credentials,
//                ],
//            ];
//
//            // GET EMPLOYERS
//            $employers = Employer::find()->all();
//
//            foreach($employers as $employer) {
//
//                $base_url = 'https://api.staffology.co.uk/employers/' . $employer->staffologyId . '/employees';
//                $client = new \GuzzleHttp\Client();
//
//                //GET LIST OF EMPLOYEES INSIDE OF EMPLOYER
//                try {
//
//                    $response = $client->get($base_url, $headers);
//                    $results = Json::decodeIfJson($response->getBody()->getContents(), true);
//
//                    // LOOP THROUGH LIST WITH COMPANIES
//                    foreach ($results as $result) {
//
//                        echo "test";
//
//                        Queue::push(new CreateEmployeeJob([
//                            'headers' => $headers,
//                            'employer' => $employer,
//                            'isDirector' => $result['metadata']['isDirector'] ?? false,
//                            'endpoint' => $result['url'],
//                        ]));
//
//                    }
//                } catch (\Throwable $e) {
//                    echo "---- error -----\n";
//                    var_dump($e->getMessage());
//                    echo "\n---- end error ----";
//                }
//            }
//        }
//    }
}
