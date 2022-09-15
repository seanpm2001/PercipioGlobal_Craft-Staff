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
use Exception;
use percipiolondon\staff\elements\Employee;
use percipiolondon\staff\elements\Employer;
use percipiolondon\staff\helpers\Logger;
use percipiolondon\staff\helpers\Security as SecurityHelper;
use percipiolondon\staff\jobs\CreateEmployeeJob;
use percipiolondon\staff\jobs\FetchEmployeesListJob;
use percipiolondon\staff\records\Address;
use percipiolondon\staff\records\Countries;
use percipiolondon\staff\records\DirectorshipDetails;
use percipiolondon\staff\records\Employer as EmployerRecord;
use percipiolondon\staff\records\EmploymentDetails;
use percipiolondon\staff\records\LeaverDetails;
use percipiolondon\staff\records\LeaveSettings;
use percipiolondon\staff\records\PersonalDetails;
use percipiolondon\staff\records\StarterDetails;
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
    /**
     * @param int $employeeId
     * @return array
     */
    public function getEmployeeById(int $employeeId): array
    {

        $employee = Employee::findOne($employeeId);

        if (!$employee) {
            return [];
        }

        $employee = $employee->toArray();

        $personalDetails = $this->getPersonalDetailsByEmployee($employeeId);
        if ($personalDetails) {
            $employee['personalDetails'] = $personalDetails;
        }

        $leaveSettings = $this->getLeaveSettingsByEmployee($employeeId);
        if ($leaveSettings) {
            $employee['leaveSettings'] = $leaveSettings;
        }

        $employmentDetails = $this->getEmploymentDetailsByEmployee($employeeId);
        if ($employmentDetails) {
            $employee['employmentDetails'] = $employmentDetails;
        }

        return $employee;
    }

    /**
     * @param int $employeeId
     * @return string
     */
    public function getEmployeeNameById(int $employeeId): string
    {
        $employee = Employer::findOne($employeeId);

        if ($employee) {
            return $employee['name'];
        }

        return '';
    }

    /**
     * @param int $employeeId
     * @return array
     */
    public function getPersonalDetailsByEmployee(int $employeeId, bool $parse = false): array
    {
        $personalDetails = PersonalDetails::findOne(['employeeId' => $employeeId]);

        if (!$personalDetails) {
            return [];
        }

        $personalDetails = $personalDetails->toArray();

        // address
        $address = Address::findOne(['employeeId' => $employeeId]);
        if ($address) {
            $address = $address->toArray();

            //country
            $country = Countries::findOne($address['countryId']);
            $address['country'] = $country['name'] ?? '';

            $personalDetails['address'] = $address;
        }

        if ($parse) {
            $personalDetails = $this->parsePersonalDetails($personalDetails);
        }

        return $personalDetails;
    }

    /**
     * @param int $employeeId
     * @return array
     */
    public function getLeaveSettingsByEmployee(int $employeeId): array
    {
        $leaveSettings = LeaveSettings::findOne(['employeeId' => $employeeId]);

        if (!$leaveSettings) {
            return [];
        }

        return $leaveSettings->toArray();
    }

    /**
     * @param int $employeeId
     * @return array
     */
    public function getStarterDetailsByEmployee(int $employeeId): array
    {
        $starterDetails = LeaveSettings::findOne(['employeeId' => $employeeId]);

        if (!$starterDetails) {
            return [];
        }

        return $starterDetails->toArray();
    }

    /**
     * @param int $employeeId
     * @return array
     */
    public function getEmploymentDetailsByEmployee(int $employeeId): array
    {
        $employmentDetails = EmploymentDetails::findOne(['employeeId' => $employeeId]);

        if (!$employmentDetails) {
            return [];
        }

        $employmentDetails = $employmentDetails->toArray();

        // directorship details
//        $directorshipDetails = DirectorshipDetails::findOne($employmentDetails['directorshipDetailsId']);
//        if ($directorshipDetails) {
//            $employmentDetails['directorshipDetails'] = $directorshipDetails->toArray();
//        }

        // starter details
        $starterDetails = StarterDetails::findOne(['employmentDetailsId' => $employmentDetails['id']]);

        if ($starterDetails) {
            $employmentDetails['starterDetails'] = $starterDetails->toArray();
        }

        // leaver details
        $leaverDetails = LeaverDetails::findOne(['employmentDetailsId' => $employmentDetails['id']]);
        if ($leaverDetails) {
            $employmentDetails['leaverDetails'] = $leaverDetails->toArray();
        }

        return $employmentDetails;
    }


    /* FETCHES */

    /**
     * @param array $employer
     */
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

    /**
     * @param array $employee
     * @param array $employer
     */
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


    /**
     * Checks if our database has employers that are deleted on staffology, if so, delete them on our system
     *
     * @param array $employer
     * @param array $employees
     */
    public function syncEmployees(array|Employer $employer, array $employees)
    {
        $logger = new Logger();
        $logger->stdout('↧ Sync employees of '. $employer['name']. PHP_EOL, $logger::RESET);

        $hubEmployer = is_array($employer) ? Employer::findOne(['staffologyId' => $employer['id']]) : $employer;
        $hubEmployees = Employee::findAll(['employerId' => $hubEmployer['id']]);

        foreach ($hubEmployees as $hubEmp) {

            $exists = false;

            // loop through our employees and check if the employee is still on staffology
            foreach ($employees as $emp) {
                if ($emp['id'] === $hubEmp['staffologyId']) {
                    $exists = true;
                }
            }

            // remove the employee if it doesn't exists anymore
            if (!$exists) {
                $logger->stdout('✓ Delete employee from '. $employer['name']. PHP_EOL, $logger::FG_YELLOW);
                Craft::$app->getElements()->deleteElementById($hubEmp['id']);
            }
        }
    }

    /**
     * @param array $employee
     * @param string $employeeName
     * @param array $employer
     * @throws \Throwable
     */
    public function saveEmployee(array $employee, string $employeeName, array|Employer $employer): void
    {
        $logger = new Logger();
        $logger->stdout("✓ Save employee " . $employeeName . '...', $logger::RESET);

        $employeeRecord = Employee::findOne(['staffologyId' => $employee['id']]);

        Craft::info("Staff management: save employee ".$employeeName);

        try {
            if (!$employeeRecord) {
                $employeeRecord = new Employee();
            }

            $employerRecord = is_array($employer) ? Employer::findOne(['staffologyId' => $employer['id']]) : $employer;

            $employeeRecord->employerId = $employerRecord['id'] ?? null;
            $employeeRecord->staffologyId = $employee['id'];
            $employeeRecord->siteId = Craft::$app->getSites()->currentSite->id;
            $employeeRecord->status = $employee['status'] ?? '';
            $employeeRecord->personalDetailsObject = $employee['personalDetails'] ?? null;
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

                if ($employee['leaveSettings'] ?? null) {
                    $this->saveLeaveSettings($employee['leaveSettings'], $employeeRecord->id);
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
        } catch (Exception $e) {
            $logger = new Logger();
            $logger->stdout(PHP_EOL, $logger::RESET);
            $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
            Craft::error($e->getMessage(), __METHOD__);
        }
    }

    /**
     * @param array $personalDetails
     * @param int|null $employee
     * @return PersonalDetails
     */
    public function savePersonalDetails(array $personalDetails, int $employee = null): PersonalDetails
    {
        $record = PersonalDetails::findOne(['employeeId' => $employee]);

        if (!$record) {
            $record = new PersonalDetails();
        }

        $record->employeeId = $employee;
        $record->maritalStatus = $personalDetails['maritalStatus'] ?? 'Unknown';
        $record->title = $personalDetails['title'] ?? '';
        $record->firstName = $personalDetails['firstName'] ?? '';
        $record->middleName = $personalDetails['middleName'] ?? '';
        $record->lastName =$personalDetails['lastName'] ?? '';
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

    /**
     * @param array $employmentDetails
     * @param int|null $employee
     * @return EmploymentDetails
     */
    public function saveEmploymentDetails(array $employmentDetails, int $employee = null): EmploymentDetails
    {
        $record = EmploymentDetails::findOne(['employeeId' => $employee]);

        if (!$record) {
            $record = new EmploymentDetails();
        }

        $record->employeeId = $employee;
        $record->cisSubContractor = $employmentDetails['cisSubCopntractor'] ?? null;
        $record->payrollCode = SecurityHelper::encrypt($employmentDetails['payrollCode'] ?? '');
        $record->jobTitle = $employmentDetails['jobTitle'] ?? '';
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

        $success = $record->save();

        if($success) {
            $this->saveStarterDetails($employmentDetails['starterDetails'], $record->id);
        }

        return $record;
    }

    /**
     * @param array $starterDetails
     * @param int|null $employmentDetailsId
     * @return StarterDetails
     */
    public function saveStarterDetails(array $starterDetails, int $employmentDetailsId = null): StarterDetails
    {
        $record = StarterDetails::findOne(['employmentDetailsId' => $employmentDetailsId]);

        if (!$record) {
            $record = new StarterDetails();
        }

        $record->employmentDetailsId = $employmentDetailsId;
        $record->startDate = $starterDetails['startDate'] ?? null;
        $record->starterDeclaration = $starterDetails['starterDeclaration'] ?? null;
        // overseasEmployerDetailsId
        // pensionerPayrollId

        $record->save();

        return $record;
    }

    /**
     * @param array $leaveSettings
     * @param int|null $employeeId
     * @return LeaveSettings
     */
    public function saveLeaveSettings(array $leaveSettings, int $employeeId = null): LeaveSettings
    {
        $record = LeaveSettings::findOne(['employeeId' => $employeeId]);

        if (!$record) {
            $record = new LeaveSettings();
        }

        $record->employeeId = $employeeId;
        $record->useDefaultHolidayType = $leaveSettings['useDefaultHolidayType'] ?? null;
        $record->useDefaultAllowanceResetDate = $leaveSettings['useDefaultAllowanceResetDate'] ?? null;
        $record->useDefaultAllowance = $leaveSettings['useDefaultAllowance'] ?? null;
        $record->useDefaultAccruePaymentInLieu = $leaveSettings['useDefaultAccruePaymentInLieu'] ?? null;
        $record->useDefaultAccruePaymentInLieuRate = $leaveSettings['useDefaultAccruePaymentInLieuRate'] ?? null;
        $record->useDefaultAccruePaymentInLieuAllGrossPay = $leaveSettings['useDefaultAccruePaymentInLieuAllGrossPay'] ?? null;
        $record->useDefaultAccruePaymentInLieuPayAutomatically = $leaveSettings['useDefaultAccruePaymentInLieuPayAutomatically'] ?? null;
        $record->useDefaultAccrueHoursPerDay = $leaveSettings['useDefaultAccrueHoursPerDay'] ?? null;
        $record->allowanceResetDate = $leaveSettings['allowanceResetDate'] ?? null;
        $record->allowance = $leaveSettings['allowance'] ?? null;
        $record->adjustment = $leaveSettings['adjustment'] ?? null;
        $record->allowanceUsed = $leaveSettings['allowanceUsed'] ?? null;
        $record->allowanceUsedPreviousPeriod = $leaveSettings['allowanceUsedPreviousPeriod'] ?? null;
        $record->allowanceRemaining = $leaveSettings['allowanceRemaining'] ?? null;
        $record->holidayType = $leaveSettings['holidayType'] ?? null;
        $record->accrueSetAmount = $leaveSettings['accrueSetAmount'] ?? null;
        $record->accrueHoursPerDay = $leaveSettings['accrueHoursPerDay'] ?? null;
        $record->showAllowanceOnPayslip = $leaveSettings['showAllowanceOnPayslip'] ?? null;
        $record->showAhpOnPayslip = $leaveSettings['showAhpOnPayslip'] ?? null;
        $record->accruePaymentInLieuRate = $leaveSettings['accruePaymentInLieuRate'] ?? null;
        $record->accruePaymentInLieuAllGrossPay = $leaveSettings['accruePaymentInLieuAllGrossPay'] ?? null;
        $record->accruePaymentInLieuPayAutomatically = $leaveSettings['accruePaymentInLieuPayAutomatically'] ?? null;
        $record->accruedPaymentLiability = $leaveSettings['accruedPaymentLiability'] ?? null;
        $record->accruedPaymentAdjustment = $leaveSettings['accruedPaymentAdjustment'] ?? null;
        $record->accruedPaymentPaid = $leaveSettings['accruedPaymentPaid'] ?? null;
        $record->accruedPaymentBalance = $leaveSettings['accruedPaymentBalance'] ?? null;

        $record->save();

        return $record;
    }


    /* PARSE SECURITY VALUES */
    /**
     * @param array $employee
     * @return array
     */
    public function parseEmployee(array $employee): array
    {
        $employee['niNumber'] = SecurityHelper::decrypt($employee['niNumber'] ?? '');
        $employee['personalDetails'] = $this->parsePersonalDetails($employee['personalDetails']);
        $employee['employmentDetails'] = $this->parseEmploymentDetails($employee['employmentDetails']);

        return $employee;
    }

    /**
     * @param array $personalDetails
     * @return array
     */
    public function parsePersonalDetails(array $personalDetails): array
    {
        $personalDetails['title'] = $personalDetails['title'] ?? '';
        $personalDetails['firstName'] = $personalDetails['firstName'] ?? '';
        $personalDetails['middleName'] = $personalDetails['middleName'] ?? '';
        $personalDetails['lastName'] = $personalDetails['lastName'] ?? '';
        $personalDetails['email'] = SecurityHelper::decrypt($personalDetails['email'] ?? '');
        $personalDetails['pdfPassword'] = SecurityHelper::decrypt($personalDetails['pdfPassword'] ?? '');
        $personalDetails['telephone'] = SecurityHelper::decrypt($personalDetails['telephone'] ?? '');
        $personalDetails['mobile'] = SecurityHelper::decrypt($personalDetails['mobile'] ?? '');
        $personalDetails['niNumber'] = SecurityHelper::decrypt($personalDetails['niNumber'] ?? '');
        $personalDetails['passportNumber'] = SecurityHelper::decrypt($personalDetails['passportNumber'] ?? '');
        $personalDetails['address'] = Staff::$plugin->addresses->parseAddress($personalDetails['address']);

        return $personalDetails;
    }

    /**
     * @param array $employmentDetails
     * @return array
     */
    public function parseEmploymentDetails(array $employmentDetails): array
    {
        $employmentDetails['payrollCode'] = SecurityHelper::decrypt($employmentDetails['payrollCode'] ?? '');
        $employmentDetails['jobTitle'] = $employmentDetails['jobTitle'] ?? '';
        $employmentDetails['furloughCalculationBasisAmount'] = SecurityHelper::decrypt($employmentDetails['furloughCalculationBasisAmount'] ?? '');
        $employmentDetails['forcePreviousPayrollCode'] = SecurityHelper::decrypt($employmentDetails['forcePreviousPayrollCode'] ?? '');

        return $employmentDetails;
    }
}
