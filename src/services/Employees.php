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
use percipiolondon\staff\elements\Employee;
use percipiolondon\staff\helpers\Logger;
use percipiolondon\staff\helpers\Security as SecurityHelper;
use percipiolondon\staff\jobs\CreateEmployeeJob;
use percipiolondon\staff\jobs\FetchEmployeesListJob;
use percipiolondon\staff\records\Employer as EmployerRecord;
use percipiolondon\staff\records\EmploymentDetails;
use percipiolondon\staff\records\PersonalDetails;
use percipiolondon\staff\Staff;

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
 * @property-read Addresses $addresses
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
                'employer' => $employer,
            ],
        ]));
    }

    public function fetchEmployee(array $employee, array $employer)
    {
        $queue = Craft::$app->getQueue();
        $queue->push(new CreateEmployeeJob([
            'description' => 'Save employees',
            'criteria' => [
                'employee' => $employee,
                'employer' => $employer,
            ],
        ]));
    }


    /* SAVES */
    public function saveEmployee(array $employee, string $employeeName, array $employer)
    {
        $logger = new Logger();
        $logger->stdout("✓ Save employee " . $employeeName . '...', $logger::RESET);

        $employeeRecord = Employee::findOne(['staffologyId' => $employee['id']]);

        try {
            if (!$employeeRecord) {
                $employeeRecord = new Employee();
            }

            $employerRecord = EmployerRecord::findOne(['staffologyId' => $employer['id']]);

            $employeeRecord->employerId = $employerRecord['id'] ?? null;
            $employeeRecord->staffologyId = $employee['id'];
            $employeeRecord->siteId = Craft::$app->getSites()->currentSite->id;
            $employeeRecord->status = $employee['status'] ?? '';
            $employeeRecord->personalDetails = $employee['personalDetails'] ?? null;
            $employeeRecord->niNumber = $employee['personalDetails']['niNumber'] ?? null;
            $employeeRecord->userId = null;
            $employeeRecord->isDirector = $this->isDirector ?? false;

            // save new employee
            $elementsService = Craft::$app->getElements();
            $success = $elementsService->saveElement($employeeRecord);

            if ($success) {
                $logger->stdout(" done" . PHP_EOL, $logger::FG_GREEN);

                //Save relations (FKs)
                if ($employee['personalDetails'] ?? null) {
                    $this->savePersonalDetails($employee['personalDetails'], $employeeRecord->id);
                }

                if ($employee['employmentDetails'] ?? null) {
                    $this->saveEmploymentDetails($employee['employmentDetails'], $employeeRecord->id);
                }
            } else {
                $logger->stdout(" failed" . PHP_EOL, $logger::FG_RED);

                $errors = "";

                foreach ($employeeRecord->errors as $err) {
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

    public function savePersonalDetails(array $personalDetails, int $employee = null): PersonalDetails
    {
        $record = PersonalDetails::findOne(['employeeId' => $employee]);

        if (!$record) {
            $record = new PersonalDetails();
        }

        $record->employeeId = $employee;
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

        $success = $record->save();

        if ($success) {
            Staff::$plugin->addresses->saveAddressByEmployee($personalDetails['address'] ?? [], $employee);
        }

        return $record;
    }

    public function saveEmploymentDetails(array $employmentDetails, int $employee = null): EmploymentDetails
    {
        $record = EmploymentDetails::findOne(['employeeId' => $employee]);

        if (!$record) {
            $record = new EmploymentDetails();
        }

        $record->employeeId = $employee;
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
