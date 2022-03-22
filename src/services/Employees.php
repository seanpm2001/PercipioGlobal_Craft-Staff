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

use craft\elements\User;
use percipiolondon\staff\helpers\Logger;
use percipiolondon\staff\helpers\Security as SecurityHelper;
use percipiolondon\staff\elements\Employee;
use percipiolondon\staff\jobs\CreateEmployeeJob;

use percipiolondon\staff\records\EmploymentDetails;
use percipiolondon\staff\records\Permission;
use percipiolondon\staff\records\PersonalDetails;
use percipiolondon\staff\records\Employee as EmployeeRecord;
use percipiolondon\staff\records\Employer as EmployerRecord;

use percipiolondon\staff\Staff;
use percipiolondon\staff\jobs\FetchEmployeesListJob;

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


    /* GETTERS */



    /* FETCHES */
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






    /* SAVES */
    public function saveEmployee(array $employee, string $employeeName, array $employer)
    {
        $logger = new Logger();
        $logger->stdout("✓ Save employee " .$employeeName . '...', $logger::RESET);

        $employeeRecord = Employee::findOne(['staffologyId' => $employee['id']]);

        try {

            if (!$employeeRecord) {
                $employeeRecord = new Employee();
            }

            //foreign keys
            $personalDetailsId = $employeeRecord->personalDetailsId ?? null;
            $employmentDetailsId = $employeeRecord->employmentDetailsId ?? null;

            $personalDetails = $this->savePersonalDetails($employee['personalDetails'], $personalDetailsId);
            $employmentDetails = $this->saveEmploymentDetails($employee['employmentDetails'], $employmentDetailsId);
            $employerRecord = EmployerRecord::findOne(['staffologyId' => $employer['id']]);

            $employeeRecord->employerId = $employerRecord['id'] ?? null;
            $employeeRecord->staffologyId = $employee['id'];
            $employeeRecord->siteId = Craft::$app->getSites()->currentSite->id;
            $employeeRecord->personalDetailsId = $personalDetails->id ?? null;
            $employeeRecord->employmentDetailsId = $employmentDetails->id ?? null;
            $employeeRecord->leaveSettingsId = $employee['leaveSettings'] ?? null;
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

        } catch (\Exception $e) {

            $logger = new Logger();
            $logger->stdout(PHP_EOL, $logger::RESET);
            $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
            Craft::error($e->getMessage(), __METHOD__);
        }

    }

    public function saveEmploymentDetails(array $employmentDetails, int $employmentDetailsId = null): EmploymentDetails
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
        $record->payrollCode = SecurityHelper::encrypt($employmentDetails['payrollCode'] ?? '');
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




    /* PARSE SECURITY VALUES */
    public function parsePersonalDetails(array $personalDetails): array
    {
        $personalDetails['title'] = SecurityHelper::decrypt($personalDetails['title'] ?? '');
        $personalDetails['firstName'] = SecurityHelper::decrypt($personalDetails['firstName'] ?? '');
        $personalDetails['middleName'] = SecurityHelper::decrypt($personalDetails['middleName'] ?? '');
        $personalDetails['lastName'] = SecurityHelper::decrypt($personalDetails['lastName'] ?? '');
        $personalDetails['email'] = SecurityHelper::decrypt($personalDetails['email'] ?? '');
        $personalDetails['pdfPassword'] = SecurityHelper::decrypt($personalDetails['pdfPassword'] ?? '');
        $personalDetails['telephone'] = SecurityHelper::decrypt($personalDetails['telephone'] ?? '');
        $personalDetails['mobile'] = SecurityHelper::decrypt($personalDetails['mobile'] ?? '');
        $personalDetails['niNumber'] = SecurityHelper::decrypt($personalDetails['niNumber'] ?? '');
        $personalDetails['passportNumber'] = SecurityHelper::decrypt($personalDetails['passportNumber'] ?? '');

        return $personalDetails;
    }

    public function parseEmploymentDetails(array $employmentDetails): array
    {
        $employmentDetails['payrollCode'] = SecurityHelper::decrypt($employmentDetails['payrollCode'] ?? '');
        $employmentDetails['jobTitle'] = SecurityHelper::decrypt($employmentDetails['jobTitle'] ?? '');
        $employmentDetails['furloughCalculationBasisAmount'] = SecurityHelper::decrypt($employmentDetails['furloughCalculationBasisAmount'] ?? '');
        $employmentDetails['forcePreviousPayrollCode'] = SecurityHelper::decrypt($employmentDetails['forcePreviousPayrollCode'] ?? '');

        return $employmentDetails;
    }
}
