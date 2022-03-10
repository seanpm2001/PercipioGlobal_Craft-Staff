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

        $employeeRecord = EmployeeRecord::findOne(['staffologyId' => $employee['id']]);
        $isNew = false;
        $user = null;

        try {

            if (!$employeeRecord) {
                $employeeRecord = new EmployeeRecord();
                $isNew = true;
            }

            //foreign keys
            $personalDetailsId = $employeeRecord->personalDetailsId ?? null;
            $employmentDetailsId = $employeeRecord->employmentDetailsId ?? null;


            // user creation
            if($employee['personalDetails'] && array_key_exists('email', $employee['personalDetails'])) {
                $user = User::findOne(['email' => $employee['personalDetails']['email']]);

                // check if user exists, if so, skip this step
                if(!$user) {

                    //create user
                    $user = new User();
                    $user->firstName = $employee['personalDetails']['firstName'];
                    $user->lastName = $employee['personalDetails']['lastName'];
                    $user->username = $employee['personalDetails']['email'];
                    $user->email = $employee['personalDetails']['email'];

                    $success = Craft::$app->elements->saveElement($user, true);

                    if(!$success){
                        throw new Exception("The user couldn't be created");
                    }

                    Craft::info("Staff: new user creation: ".$user->id);

                    // assign user to group
                    $group = Craft::$app->getUserGroups()->getGroupByHandle('hardingUsers');
                    if($group){
                        Craft::$app->getUsers()->assignUserToGroups($user->id, [$group->id]);
                    }
                }
            }

            //foreign keys
            $personalDetails = Staff::$plugin->employees->savePersonalDetails($employee['personalDetails'], $personalDetailsId);
            $employmentDetails = Staff::$plugin->employees->saveEmploymentDetails($employee['employmentDetails'], $employmentDetailsId);
            $employerId = EmployerRecord::findOne(['staffologyId' => $employer['id']]);

            $employeeRecord->employerId = $employerId->id ?? null;
            $employeeRecord->userId = $user->id ?? null;
            $employeeRecord->personalDetailsId = $personalDetails->id ?? null;
            $employeeRecord->employmentDetailsId = $employmentDetails->id ?? null;
            $employeeRecord->staffologyId = $employee['id'] ?? null;
            $employeeRecord->status = $employee['status'] ?? null;
            $employeeRecord->sourceSystemId = $employee['sourceSystemId'] ?? null;
            $employeeRecord->niNumber = SecurityHelper::encrypt($employee['niNumber'] ?? '');
            $employeeRecord->isDirector = $employee['employmentDetails']['directorshipDetails']['isDirector'] ?? null;

            $success = $employeeRecord->save();

            if($isNew && $user) {
                //assign permissions to employee
                if($employeeRecord->isDirector) {
                    $permissions = Permission::find()->all();
                } else {
                    $permissions = [Permission::findOne(['name' => 'access:employer'])];
                }

                Staff::$plugin->userPermissions->createPermissions($permissions, $user->id, $employeeRecord->id);
            }

            if($success){
                $logger->stdout(" done" . PHP_EOL, $logger::FG_GREEN);

                $this->saveEmployeeElement();
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






    /* SAVES ELEMENTS */
    public function saveEmployeeElement(): bool
    {
        $employeeRecord = new Employee();
        $elementsService = Craft::$app->getElements();
        return $elementsService->saveElement($employeeRecord);

    }
}
