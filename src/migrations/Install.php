<?php
/**
 * staff-management plugin for Craft CMS 3.x
 *
 * Craft Staff Management provides an HR solution for payroll and benefits
 *
 * @link      http://percipio.london
 * @copyright Copyright (c) 2021 Percipio
 */

namespace percipiolondon\staff\migrations;

use craft\helpers\MigrationHelper;
use percipiolondon\staff\db\Table;

use Craft;
use craft\config\DbConfig;
use craft\db\ActiveRecord;
use craft\db\Migration;
use craft\db\Query;
use yii\base\NotSupportedException;

/**
 * Installation Migration
 *
 *
 * @author    Percipio Global Ltd. <support@percipio.london>
 * @since     1.0.0
 */
class Install extends Migration
{


    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        if ($this->createTables()) {
            $this->createIndexes();
            $this->addForeignKeys();
            Craft::$app->db->schema->refresh();
            $this->insertDefaultData();
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {

        $this->dropForeignKeys();
        $this->removeTables();

        return true;
    }

    /**
     * Creates the tables for Staff Management
     */

    public function createTables()
    {
        $this->createTable(Table::ADDRESSES, [
            'id' => $this->primaryKey(),
            'countryId' => $this->integer(),
            'countyId' => $this->integer(),
            'address1' => $this->string(),
            'address2' => $this->string(),
            'address3' => $this->string(),
            'zipCode' => $this->string(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
        ]);

        $this->createTable(Table::AUTO_ENROLMENT, [
            'id' => $this->primaryKey(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
            'state' => $this->enum('state', ['Automatic', 'OptOut', 'OptIn', 'VoluntaryJoiner', 'ContractualPension', 'CeasedMembership', 'Leaver', 'Excluded', 'Enrol']),
            'stateDate' => $this->dateTime(),
            'ukWorker' => $this->enum('status', ['No', 'Yes', 'Ordinarily']),
            'daysToDeferAssessment' => $this->integer(),
            'postponementData' => $this->dateTime(),
            'deferByMonthsNotDays' => $this->boolean(),
            'exempt' => $this->boolean(),
            'aeExclusionCode' => $this->enum('code', ['NotKnown', 'NotAWorker', 'NotWorkingInUk', 'NoOrdinarilyWorkingInUk', 'OutsideOfAgeRange', 'SingleEmployee', 'CeasedActiveMembershipInPast12Mo', 'CeasedActiveMembership', 'ReceivedWulsInPast12Mo', 'ReceivedWuls', 'Leaving', 'TaxProtection', 'CisSubContractor']),
            'aePostponementLetterSent' => $this->boolean(),
            // Link to AeAssessment Table
            'lastAssessment' => $this->integer(),
        ]);

        $this->createTable(Table::AUTO_ENROLMENT_ASSESSMENT, [
            'id' => $this->primaryKey(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
            'assessmentDate' => $this->dateTime(),
            'employeeState' => $this->enum('state', ['Automatic', 'OptOut', 'OptIn', 'VoluntaryJoiner', 'ContractualPension', 'CeasedMembership', 'Leaver', 'Excluded', 'Enrol']),
            'age' => $this->integer(),
            'ukWorker' => $this->enum('status', ['No', 'Yes', 'Ordinarily']),
            'payPeriod' => $this->enum('period', ['Custom', 'Monthly', 'FourWeekly', 'Fortnightly', 'Weekly', 'Daily']),
            'ordinal' => $this->integer(),
            'earningsInPeriod' => $this->double(),
            'qualifyingEarningsInPeriod' => $this->double(),
            'aeExclusionCode' => $this->enum('code', ['NotKnown', 'NotAWorker', 'NotWorkingInUk', 'NoOrdinarilyWorkingInUk', 'OutsideOfAgeRange', 'SingleEmployee', 'CeasedActiveMembershipInPast12Mo', 'CeasedActiveMembership', 'ReceivedWulsInPast12Mo', 'ReceivedWuls', 'Leaving', 'TaxProtection', 'CisSubContractor']),
            'status' => $this->enum('status', ['Eligible', 'NonEligible', 'Entitled', 'NoDuties']),
            'reason' => $this->string(),
            // Link to AeAssessmentAction Table
            'action' => $this->integer(),
            // Link to Item Table
            'employee' => $this->integer(),
            'assessmentId' => $this->uid(),
        ]);

        $this->createTable(Table::BANK_DETAILS, [
            'id' => $this->primaryKey(),
            'bankName' => $this->string(),
            'bankBranch' => $this->string(),
            'bankReference' => $this->string(),
            'accountName' => $this->string(),
            'accountNumber' => $this->string(),
            'sortCode' => $this->string(),
            'note' => $this->note(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
        ]);

        $this->createTable(Table::CIS_DETAILS, [
            'id' => $this->primaryKey(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
            'type' => $this->enum('type', ['SoleTrader', 'Partnership', 'Company', 'Trust']),
            'utr' => $this->string(),
            'tradingName' => $this->string(),
            'companyUtr' => $this->string(),
            'companyNumber' => $this->string(),
            'vatRegistered' => $this->boolean(),
            'vatNumber' => $this->string(),
            'vatRate' => $this->double(),
            'reverseChargeVAT' => $this->boolean(),
            // CisVerificationDetails Table
            'verification' => $this->integer(),
        ]);

        $this->createTable(Table::CIS_PARTNERSHIP, [
            'id' => $this->primaryKey(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
            'name' => $this->string(),
            'utr' => $this->string(),
        ]);

        $this->createTable(Table::CIS_SUBCONTRACTOR, [
            'id' => $this->primaryKey(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
            'employeeUniqueId' => $this->string(),
            'emailStatementTo' => $this->string(),
            'numberOfPayments' => $this->integer(),
            // Items Table
            'item' => $this->integer(),
            'displayName' => $this->string(),
            'action' => $this->string(),
            'type'  => $this->string(),
            // RTI Employee Name Table
            'name' => $this->integer(),
            'tradingName' => $this->string(),
            'worksRef' => $this->string(),
            'unmatchedRate' => $this->string(),
            'utr' => $this->string(),
            'crn' => $this->string(),
            'nino' => $this->string(),
            // CIS Partnership Table
            'partnership' => $this->integer(),
            // RTI Employee Address
            'address' => $this->integer(),
            'telephone' => $this->string(),
            'totalPaymentsUnrounded' => $this->string(),
            'costOfMaterialsUnrounded' => $this->string(),
            'umbrellaFee' => $this->string(),
            'validationMsg' => $this->string(),
            'verificationNumber' => $this->string(),
            'totalPayments' => $this->string(),
            'costOfMaterials' => $this->string(),
            'totalDeducted' => $this->string(),
            'matched' => $this->string(),
            'taxTreatment' => $this->string(),
        ]);

        $this->createTable(Table::CIS_VERIFICATION_DETAILS, [
            'id' => $this->primaryKey(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
            'manuallyEntered' => $this->boolean(),
            'matchInsteadOfVerify' => $this->boolean(),
            'number' => $this->string(),
            'date' => $this->dateTime(),
            'taxStatus' => $this->enum('status', ['Gross', 'NetOfStandardDeduction', 'NotOfHigherDeduction']),
            'verificationRequest' => $this->string(),
            // CisSubContractor Table
            'verificationResponse' => $this->integer(),
        ]);

        $this->createTable(Table::COUNTRIES, [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'iso' => $this->string(3)->notNull(),
            'sortOrder' => $this->integer(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
        ]);

        $this->createTable(Table::DEPARTMENT, [
            'id' => $this->primaryKey(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
            'code' => $this->string(),
            'title' => $this->string(),
            // @DISCUSS - needed? Could use better things
            'color' => $this->string(),
            // @DISCUSS - needed? Can query this ourselves
            'employeeCount' => $this->integer(),
        ]);

        $this->createTable(Table::DIRECTORSHIP_DETAILS, [
            'id' => $this->primaryKey(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
            'isDirector' => $this->boolean(),
            'startDate' => $this->dateTime(),
            'leaveDate' => $this->dateTime(),
            'niAlternativeMethod' => $this->boolean(),
        ]);

        $this->createTable(Table::EMPLOYEES, [
            'id' => $this->primaryKey(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
            // @TODO: drop siteId in migration
            'siteId' => $this->integer()->notNull(),
            'staffologyId' => $this->string(255)->notNull(),
            'employerId' => $this->integer()->notNull(),
            'userId' => $this->integer(),
            'isDirector' => $this->boolean(),
            // @TODO: create ID to table ( FK )
            'personalDetails' => $this->longText(),
            // @TODO: create own table
            'employmentDetails' => $this->longText(),
            // @TODO: create own table
            'autoEnrolment' => $this->longText(),
            // @TODO: create ID to table ( FK )
            'leaveSettings' => $this->longText(),
            // @TODO: create own table
            'rightToWork' => $this->longText(),
            // @TODO: create ID to table ( FK )
            'bankDetails' => $this->longText(),
            'status' => $this->string(255)->notNull()->defaultValue('Current'),
            'aeNotEnroledWarning' => $this->boolean()->defaultValue(0),
            'niNumber' => $this->string(255),
            'sourceSystemId' => $this->string(255),
        ]);

        $this->createTable(Table::EMPLOYERS, [
            'id' => $this->primaryKey(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
            'slug' => $this->string(255)->notNull(),
            // @TODO: drop siteId in migration
            'siteId' => $this->integer()->notNull(),
            'staffologyId' => $this->string(255)->notNull(),
            'name' => $this->string(255)->notNull(),
            'crn' => $this->string(),
            // @TODO: create ID to table ( FK )
            'address' => $this->longText(),
            // @TODO: create own table
            'hmrcDetails' => $this->longText(),
            'startYear' => $this->string(255)->notNull(),
            'currentYear' => $this->string(255)->notNull(),
            'employeeCount' => $this->integer()->notNull()->defaultValue(0),
            // @TODO: create own table
            'defaultPayOptions' => $this->longText(),
        ]);

        $this->createTable(Table::EMPLOYMENT_DETAILS, [
            'id' => $this->primaryKey(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
            'cisSubContractor' => $this->boolean(),
            'payrollCode' => $this->string(),
            'jobTitle' => $this->string(),
            'onHold' => $this->boolean(),
            'onFurlough' => $this->boolean(),
            'furloughStart' => $this->dateTime(),
            'furloughEnd' => $this->dateTime(),
            'furloughCalculationBasis' => $this->enum('calculation', ['ActualPaidAmount', 'DailyReferenceAmount', 'MonthlyReferenceAmount']),
            'furloughCalculationBasisAmount' => $this->double(),
            'partialFurlough' => $this->boolean(),
            'furloughHoursNormallyWorked' => $this->double(),
            'furloughHoursOnFurlough' => $this->double(),
            'isApprentice' => $this->boolean(),
            'apprenticeshipStartDate' => $this->dateTime(),
            'apprenticeshipEndDate' => $this->dateTime(),
            'workingPattern' => $this->string(),
            'forcePreviousPayrollCode' => $this->string(),
            'starterDetails' => $this->integer(),
            'directorshipDetails' => $this->integer(),
            'leaverDetails' => $this->integer(),
            'cis' => $this->integer(),
            'department' => $this->integer(),
            // @DISCUSS - Staffology provides an Item Array, so should hold multiple id's?
            'posts' => $this->integer(),
        ]);

        $this->createTable(Table::HISTORY, [
            'id' => $this->primaryKey(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
            'employerId' => $this->integer()->notNull(),
            'employeeId' => $this->integer()->notNull(),
            // This could be null
            'administerId' => $this->integer(),
            'message' => $this->string(255)->notNull(),
            'type' => $this->string()->notNull(),
        ]);

        $this->createTable(Table::ITEM, [
            'id' => $this->primaryKey(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
            'itemId' => $this->string(),
            'name' => $this->string(),
            'metadata' => $this->longText(),
            'url' => $this->string(),
        ]);

        $this->createTable(Table::LEAVE_SETTINGS, [
            'id' => $this->primaryKey(),
            'useDefaultHolidayType' => $this->boolean(),
            'useDefaultAllowanceResetDate' => $this->boolean(),
            'useDefaultAllowance' => $this->boolean(),
            'useDefaultAccruePaymentInLieu' => $this->boolean(),
            'useDefaultAccruePaymentInLieuRate' => $this->boolean(),
            'useDefaultAccruePaymentInLieuAllGrossPay' => $this->boolean(),
            'useDefaultAccruePaymentInLieuPayAutomatically' => $this->boolean(),
            'useDefaultAccrueHoursPerDay' => $this->boolean(),
            'allowanceResetDate' => $this->dateTime(),
            'allowance' => $this->double(),
            'adjustment' => $this->double(),
            'allowanceUsed' => $this->double(),
            'allowanceUsedPreviousPeriod' => $this->double(),
            'allowanceRemaining' => $this->double(),
            'holidayType' => $this->enum('type', ['Days', 'Accrual_Money', 'Accrual_Days']),
            'accrueSetAmount' => $this->boolean(),
            'accrueHoursPerDay' => $this->double(),
            'showAllowanceOnPayslip' => $this->boolean(),
            'showAhpOnPayslip' => $this->boolean(),
            'accruePaymentInLieuRate' => $this->double(),
            'accruePaymentInLieuAllGrossPay' => $this->boolean(),
            'accruePaymentInLieuPayAutomatically' => $this->boolean(),
            'accruedPaymentLiability' => $this->double(),
            'accruedPaymentAdjustment' => $this->double(),
            'accruedPaymentPaid' => $this->double(),
            'accruedPaymentBalance' => $this->double(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
        ]);

        $this->createTable(Table::LEAVER_DETAILS, [
            'id' => $this->primaryKey(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
            'hasLeft' => $this->boolean(),
            'leaveDate' => $this->dateTime(),
            'isDeceased' => $this->boolean(),
            'paymentAfterLeaving' => $this->boolean(),
            'p45Sent' => $this->boolean(),
        ]);

        $this->createTable(Table::OVERSEAS_EMPLOYER_DETAILS, [
            'id' => $this->primaryKey(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
            'overseasEmployer' => $this->boolean(),
            'overseasSecondmentStatus' => $this->enum('status', ['MoreThan183Days', 'LessThan183Days', 'BothInAndOutOfUK']),
            'eeaCitizen' => $this->boolean(),
            'epm6Scheme' => $this->boolean(),
        ]);

        $this->createTable(Table::PAYRUN, [
            'id' => $this->primaryKey(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
            // @TODO: drop siteId in migration
            'siteId' => $this->integer()->notNull(),
            'staffologyId' => $this->string(255),
            'taxYear' => $this->string(255)->notNull()->defaultValue(''),
            'taxMonth' => $this->integer()->notNull()->defaultValue(0),
            'payPeriod' => $this->string(255)->notNull()->defaultValue(''),
            'ordinal' => $this->integer()->notNull()->defaultValue(1),
            'period' => $this->integer()->notNull()->defaultValue(1),
            'startDate' => $this->dateTime()->notNull(),
            'endDate' => $this->dateTime()->notNull(),
            'paymentDate' => $this->dateTime()->notNull(),
            'employeeCount' => $this->integer()->notNull()->defaultValue(0),
            'subContractorCount' => $this->integer()->notNull()->defaultValue(0),
            // @TODO: create own table
            'totals' => $this->longText()->notNull(),
            'state' => $this->string(255)->notNull()->defaultValue(''),
            'isClosed' => $this->boolean()->notNull(),
            'dateClosed' => $this->dateTime(),
            'url' => $this->string()->defaultValue(''),
            'employerId' => $this->integer()->notNull()->defaultValue(null),
        ]);

        $this->createTable(Table::PAYRUN_LOG, [
            'id' => $this->primaryKey(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
            // @TODO: drop siteId in migration
            'siteId' => $this->integer()->notNull(),
            // Custom columns in the table
            'employeeCount' => $this->integer()->notNull()->defaultValue(0),
            'taxYear' => $this->string(255)->notNull()->defaultValue(''),
            'lastPeriodNumber' => $this->integer()->notNull()->defaultValue(0),
            'url' => $this->string(255)->notNull()->defaultValue(0),
            'employerId' => $this->integer()->notNull()->defaultValue(0),
            'payRunId' => $this->integer()->notNull()->defaultValue(0),
        ]);

        $this->createTable(Table::PAYRUN_ENTRIES, [
            'id' => $this->primaryKey(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
            // @TODO: drop siteId in migration
            'siteId' => $this->integer()->notNull(),
            // Custom columns in the table
            'staffologyId' => $this->string(255)->notNull(),
            'payRunId' => $this->integer()->notNull()->defaultValue(0),
            'taxYear' => $this->string(255)->defaultValue(''),
            'startDate' => $this->dateTime(),
            'endDate' => $this->dateTime(),
            // @TODO: create own table
            'note' => $this->longText(),
            'bacsSubReference' => $this->string(255)->defaultValue(''),
            'bacsHashcode' => $this->string(255)->defaultValue(''),
            'percentageOfWorkingDaysPaidAsNormal' => $this->double()->defaultValue(0),
            'workingDaysNotPaidAsNormal' => $this->double()->defaultValue(0),
            'payPeriod' => $this->string(255)->defaultValue(''),
            'ordinal' => $this->integer()->defaultValue(1),
            'period' => $this->integer()->defaultValue(1),
            'isNewStarter' => $this->boolean(),
            'unpaidAbsence' => $this->boolean(),
            'hasAttachmentOrders' => $this->boolean(),
            'paymentDate' => $this->dateTime(),
            'priorPayrollCode' => $this->string(255)->defaultValue(''),
            // @TODO: create own table
            'payOptions' => $this->longText(),
            // @TODO: create own table
            'pensionSummary' => $this->longText(),
            // @TODO: create own table
            'totals' => $this->longText(),
            // @TODO: create own table
            'periodOverrides' => $this->longText(),
            // @TODO: create own table
            'totalsYtd' => $this->longText(),
            // @TODO: create own table
            'totalsYtdOverrides' => $this->longText(),
            'forcedCisVatAmount' => $this->double()->defaultValue(0),
            'holidayAccrued' => $this->double()->defaultValue(0),
            'state' => $this->string(255)->defaultValue('Open'),
            'isClosed' => $this->boolean(),
            'manualNi' => $this->boolean(),
            // @TODO: create own table
            'nationalInsuranceCalculation' => $this->longText(),
            'payrollCodeChanged' => $this->boolean(),
            'aeNotEnroledWarning' => $this->boolean(),
            'fps' => $this->longText(),
            'receivingOffsetPay' => $this->boolean(),
            'paymentAfterLearning' => $this->boolean(),
            // @TODO: create own table
            'umbrellaPayment' => $this->longText(),
            // @TODO: create own table
            'employee' => $this->longText(),
            'pdf' => $this->string()->defaultValue(''),
            'employerId' => $this->integer()->notNull()->defaultValue(null),
            'employeeId' => $this->integer()->notNull()->defaultValue(null),
        ]);

        $this->createTable(Table::PENSIONER_PAYROLL, [
            'id' => $this->primaryKey(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
            'inReceiptOfPension' => $this->boolean(),
            'bereaved' => $this->boolean(),
            'amount' => $this->double(),
        ]);

        $this->createTable(Table::PERMISSIONS, [
            'id' => $this->primaryKey(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
            'name' => $this->string(255)->notNull()->defaultValue(''),
        ]);

        $this->createTable(Table::PERMISSIONS_USERS, [
            'id' => $this->primaryKey(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
            'permissionId' => $this->integer()->notNull()->defaultValue(0),
            'userId' => $this->integer()->defaultValue(null),
            'employeeId' => $this->integer()->notNull()->defaultValue(0),
        ]);

        // staff_personal_details table
        // TODO:
        // "PersonalDetails": {
        //      "PartnerDetails": null
        //    },
        $this->createTable(Table::PERSONAL_DETAILS,
            [
                'id' => $this->primaryKey(),
                'employeeId' => $this->integer()->notNull(),
                'addressId' => $this->integer()->notNull(),
                'maritalStatus' => $this->enum('status', ['Single', 'Married', 'Divorced', 'Widowed', 'CivilPartnership', 'Unknown'])->notNull(),
                'title' => $this->string(255),
                'firstName' => $this->string(255)->notNull(),
                'middleName' => $this->string(255),
                'lastName' => $this->string(255)->notNull(),
                'email' => $this->string(255)->notNull(),
                'emailPayslip' => $this->boolean()->notNull(),
                'passwordProtectPayslip' => $this->boolean()->notNull(),
                'pdfPassword' => $this->string(255),
                'telephone' => $this->string(255),
                'mobile' => $this->string(255),
                'dob' => $this->dateTime()->notNull(),
                'statePensionAge' => $this->int()->notNull(),
                'gender' => $this->enum('gender', ['Male', 'Female'])->notNull(),
                'niNumber' => $this->string(255)->notNull(),
                'passportNumber' => $this->string(255)->notNull(),
            ]
        );

        $this->createTable(Table::REQUESTS, [
            'id' => $this->primaryKey(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
            'dateAdministered' => $this->dateTime()->notNull(),
            'employerId' => $this->integer()->notNull(),
            'employeeId' => $this->integer()->notNull(),
            'administerId' => $this->integer()->notNull(),
            'data' => $this->longText(),
            'section' => $this->string()->notNull(),
            'element' => $this->string()->notNull(),
            'status' => $this->string()->notNull(),
            'note' => $this->string(255),
        ]);

        $this->createTable(Table::RTI_EMPLOYEE_ADDRESS, [
            'id' => $this->primaryKey(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
            'line' => $this->longText(),
            'postcode' => $this->string(),
            'postCode' => $this->string(),
            'ukPostcode' => $this->string(),
            'country' => $this->string(),
        ]);

        $this->createTable(Table::RTI_EMPLOYEE_NAME, [
            'id' => $this->primaryKey(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
            'ttl' => $this->string(),
            'fore' => $this->longText(),
            'initials' => $this->string(),
            'sur' => $this->string(),
        ]);

        $this->createTable(Table::STARTER_DETAILS, [
            'id' => $this->primaryKey(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
            'startDate' => $this->dateTime(),
            'starterDeclaration' => $this->enum('declaration', ['A', 'B', 'C', 'Unknown']),
            'overseasEmployerDetails' => $this->integer(),
            'pensionerPayroll' => $this->integer(),
        ]);

        $this->createTable(Table::USERS, [
            'id' => $this->primaryKey(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
            // @TODO: drop siteId in migration
            'siteId' => $this->integer()->notNull(),
            // Custom columns in the table
            'staffologyId' => $this->string(255)->notNull(),
            // @TODO: create own table
            'metadata' => $this->longText()->notNull(),
        ]);

    }

    /**
     * Drop the tables
     */
    public function dropTables() {
        $this->dropTableIfExists(Table::EMPLOYEES);
        $this->dropTableIfExists(Table::EMPLOYERS);
        $this->dropTableIfExists(Table::HISTORY);
        $this->dropTableIfExists(Table::PAYRUN);
        $this->dropTableIfExists(Table::PAYRUN_LOG);
        $this->dropTableIfExists(Table::PAYRUN_ENTRIES);
        $this->dropTableIfExists(Table::PERMISSIONS);
        $this->dropTableIfExists(Table::PERMISSIONS_USERS);
        $this->dropTableIfExists(Table::PERSONAL_DETAILS);
        $this->dropTableIfExists(Table::REQUESTS);
        $this->dropTableIfExists(Table::USERS);

        return null;
    }

    /**
     * Creates the indexes
     */
    public function createIndexes()
    {
        //$this->createIndex(null, Table::STAFF_EMPLOYERS, 'name', false);
        //$this->createIndex(null, Table::STAFF_EMPLOYEES, 'niNumber', false);
        //$this->createIndex(null, Table::STAFF_REQUESTS, 'element', false);
        //$this->createIndex(null, Table::STAFF_HISTORY, 'type', false);
    }

    /**
     * Adds the foreign keys
     */
    public function addForeignKeys()
    {

    }

    /**
     * Removes the foreign keys
     */
    public function dropForeignKeys()
    {
        $tables = [
            Table::ADDRESSES,
            Table::BANK_DETAILS,
            Table::COUNTRIES,
            Table::EMPLOYEES,
            Table::EMPLOYERS,
            Table::HISTORY,
            Table::LEAVE_SETTINGS,
            Table::PAYRUN,
            Table::PAYRUN_LOG,
            Table::PAYRUN_ENTRIES,
            Table::PERMISSIONS,
            Table::PERMISSIONS_USERS,
            Table::PERSONAL_DETAILS,
            Table::REQUESTS,
            Table::USERS
        ];

        foreach ($tables as $table) {
            $this->_dropForeignKeyToAndFromTable($table);
        }
    }

    /**
     * Insert the default data.
     */
    public function insertDefaultData()
    {
        $this->_createPermissions();
        $this->_defaultCountries();
    }

    /**
     * Insert default countries data.
     */
    private function _defaultCountries()
    {
        $countries = [
            ['ENG', 'England'],
            ['NIR', 'Northern Ireland'],
            ['SCT', 'Scotland'],
            ['WLS', 'Wales'],
            ['UKM', 'United Kingdom'],
            ['OUK', 'Outside of the UK'],
        ];

        $orderNumber = 1;
        foreach ($countries as $key => $country) {
            $country[] = $orderNumber;
            $countries[$key] = $country;
            $orderNumber++;
        }

        $this->batchInsert(Table::COUNTRIES, ['iso', 'name', 'sortOrder'], $countries);
    }

    /**
     * Creates the foreign keys needed for the Records used by the plugin
     *
     * @return void
     */
    protected function addForeignKeys()
    {
        // staff_employer table
        $this->addForeignKey(
            $this->db->getForeignKeyName(Table::STAFF_EMPLOYERS, 'id'),
            Table::STAFF_EMPLOYERS,
            'id',
            \craft\db\Table::ELEMENTS,
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            $this->db->getForeignKeyName(Table::STAFF_EMPLOYERS, 'siteId'),
            Table::STAFF_EMPLOYERS,
            'siteId',
            \craft\db\Table::SITES,
            'id',
            'CASCADE',
            'CASCADE'
        );

        // staff_employee table
        $this->addForeignKey(
            $this->db->getForeignKeyName(Table::STAFF_EMPLOYEES, 'id'),
            Table::STAFF_EMPLOYEES,
            'id',
            \craft\db\Table::ELEMENTS,
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            $this->db->getForeignKeyName(Table::STAFF_EMPLOYEES, 'siteId'),
            Table::STAFF_EMPLOYEES,
            'siteId',
            \craft\db\Table::SITES,
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            $this->db->getForeignKeyName(Table::STAFF_EMPLOYEES, 'userId'),
            Table::STAFF_EMPLOYEES,
            'userId',
            \craft\db\Table::USERS,
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            $this->db->getForeignKeyName(Table::STAFF_EMPLOYEES, 'employerId'),
            Table::STAFF_EMPLOYEES,
            'employerId',
            Table::STAFF_EMPLOYERS,
            'id',
            'CASCADE',
            'CASCADE'
        );

        // staff_payrun_log table
        $this->addForeignKey(
            $this->db->getForeignKeyName(Table::STAFF_PAYRUN_LOG, 'siteId'),
            Table::STAFF_PAYRUN_LOG,
            'siteId',
            \craft\db\Table::SITES,
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            $this->db->getForeignKeyName(Table::STAFF_PAYRUN_LOG, 'employerId'),
            Table::STAFF_PAYRUN_LOG,
            'employerId',
            Table::STAFF_EMPLOYERS,
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            $this->db->getForeignKeyName(Table::STAFF_PAYRUN_LOG, 'payRunId'),
            Table::STAFF_PAYRUN_LOG,
            'payRunId',
            Table::STAFF_PAYRUN,
            'id',
            'CASCADE',
            'CASCADE'
        );

        // staff_payrun table
        $this->addForeignKey(
            $this->db->getForeignKeyName(Table::STAFF_PAYRUN, 'id'),
            Table::STAFF_PAYRUN,
            'id',
            \craft\db\Table::ELEMENTS,
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            $this->db->getForeignKeyName(Table::STAFF_PAYRUN, 'siteId'),
            Table::STAFF_PAYRUN,
            'siteId',
            \craft\db\Table::SITES,
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            $this->db->getForeignKeyName(Table::STAFF_PAYRUN, 'employerId'),
            Table::STAFF_PAYRUN,
            'employerId',
            Table::STAFF_EMPLOYERS,
            'id',
            'CASCADE',
            'CASCADE'
        );

        // staff_payrunentries table
        $this->addForeignKey(
            $this->db->getForeignKeyName(Table::STAFF_PAYRUNENTRIES, 'id'),
            Table::STAFF_PAYRUNENTRIES,
            'id',
            \craft\db\Table::ELEMENTS,
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            $this->db->getForeignKeyName(Table::STAFF_PAYRUNENTRIES, 'siteId'),
            Table::STAFF_PAYRUNENTRIES,
            'siteId',
            \craft\db\Table::SITES,
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            $this->db->getForeignKeyName(Table::STAFF_PAYRUNENTRIES, 'payRunId'),
            Table::STAFF_PAYRUNENTRIES,
            'payRunId',
            Table::STAFF_PAYRUN,
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            $this->db->getForeignKeyName(Table::STAFF_PAYRUNENTRIES, 'employerId'),
            Table::STAFF_PAYRUNENTRIES,
            'employerId',
            Table::STAFF_EMPLOYERS,
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            $this->db->getForeignKeyName(Table::STAFF_PAYRUNENTRIES, 'employeeId'),
            Table::STAFF_PAYRUNENTRIES,
            'employeeId',
            Table::STAFF_EMPLOYEES,
            'id',
            'CASCADE',
            'CASCADE'
        );

        // staff_permissions_users
        $this->addForeignKey(
            $this->db->getForeignKeyName(Table::STAFF_PERMISSIONS_USERS, 'userId'),
            Table::STAFF_PERMISSIONS_USERS,
            'userId',
            \craft\db\Table::USERS,
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            $this->db->getForeignKeyName(Table::STAFF_PERMISSIONS_USERS, 'employeeId'),
            Table::STAFF_PERMISSIONS_USERS,
            'employeeId',
            Table::STAFF_EMPLOYEES,
            'id',
            'CASCADE',
            'CASCADE'
        );

        // staff_personal_detail
        $this->addForeignKey(
            $this->db-getForeignKeyName(Table::STAFF_PERSONAL_DETAILS, 'employeeId'),
            Table::STAFF_PERSONAL_DETAILS,
            'employeeId',
            Table::STAFF_EMPLOYEES,
            'id',
            'CASCADE',
            'CASCADE'
        );

        // staff_requests
        $this->addForeignKey(
            $this->db->getForeignKeyName(Table::STAFF_REQUESTS, 'employerId'),
            Table::STAFF_REQUESTS,
            'id',
            Table::STAFF_EMPLOYERS,
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            $this->db->getForeignKeyName(Table::STAFF_REQUESTS, 'employeeId'),
            Table::STAFF_REQUESTS,
            'id',
            Table::STAFF_EMPLOYEES,
            'id',
            'CASCADE',
            'CASCADE'
        );

        // staff_user table
        $this->addForeignKey(
            $this->db->getForeignKeyName(Table::STAFF_USERS, 'siteId'),
            Table::STAFF_USERS,
            'siteId',
            \craft\db\Table::SITES,
            'id',
            'CASCADE',
            'CASCADE'
        );

        // staff_request table
        $this->addForeignKey(
            $this->db->getForeignKeyName(Table::STAFF_REQUESTS, 'employerId'),
            Table::STAFF_REQUESTS,
            'id',
            Table::STAFF_EMPLOYERS,
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            $this->db->getForeignKeyName(Table::STAFF_REQUESTS, 'employeeId'),
            Table::STAFF_REQUESTS,
            'id',
            Table::STAFF_EMPLOYEES,
            'id',
            'CASCADE',
            'CASCADE'
        );

        // staff_history table
        $this->addForeignKey(
            $this->db->getForeignKeyName(Table::STAFF_HISTORY, 'employerId'),
            Table::STAFF_HISTORY,
            'id',
            Table::STAFF_EMPLOYERS,
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            $this->db->getForeignKeyName(Table::STAFF_HISTORY, 'employeeId'),
            Table::STAFF_HISTORY,
            'id',
            Table::STAFF_EMPLOYEES,
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * Create the permissions for the Company Users
     */
    private function _createPermissions()
    {
        $rows = [];

        $rows[] = ['access:employers'];
        $rows[] = ['access:employer'];
        $rows[] = ['access:groupbenefits'];
        $rows[] = ['access:voluntarybenefits'];
        $rows[] = ['manage:notifications'];
        $rows[] = ['manage:employees'];
        $rows[] = ['manage:employer'];
        $rows[] = ['manage:benefits'];
        $rows[] = ['purchase:groupbenefits'];
        $rows[] = ['purchase:voluntarybenefits'];

        $this->batchInsert(Table::STAFF_PERMISSIONS, ['name'], $rows);
    }
    /**
     * Returns if the table exists.
     *
     * @param string $tableName
     * @param \yii\db\Migration|null $migration
     * @return bool If the table exists.
     * @throws NotSupportedException
     */
    private function _tableExists(string $tableName): bool
    {
        $schema = $this->db->getSchema();
        $schema->refresh();
        $rawTableName = $schema->getRawTableName($tableName);
        $table = $schema->getTableSchema($rawTableName);

        return (bool)$table;
    }

    /**
     * @param string $tableName
     * @throws NotSupportedException
     */
    private function _dropForeignKeyToAndFromTable(string $tableName)
    {
        if ($this->_tableExists($tableName)) {
            MigrationHelper::dropAllForeignKeysToTable($tableName, $this);
            MigrationHelper::dropAllForeignKeysOnTable($tableName, $this);
        }
    }
}
