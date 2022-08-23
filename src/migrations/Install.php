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

use Craft;

use craft\db\Migration;
use craft\db\Table as CraftTable;
use percipiolondon\staff\db\Table;

/**
 * Installation Migration
 *
 *
 * @author    Percipio Global Ltd. <support@percipio.london>
 * @since     1.0.0
 */
class Install extends Migration
{
    public $driver;

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

//        $this->dropForeignKeys();
//        $this->dropTables();

        return true;
    }

    /**
     * Creates the tables for Staff Management
     */
    public function createTables()
    {
        $tableCreated = false;

        $tableSchema = Craft::$app->db->schema->getTableSchema(Table::EMPLOYERS);
        if ($tableSchema === null) {
            // BASE
            $this->createTable(Table::BENEFIT_PROVIDERS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                'siteId' => $this->integer(),
                //FK
                //intern
                //fields
                'name' => $this->string(255)->notNull(),
                'logo' => $this->integer(),
                'url' => $this->string(255)->notNull(),
                'content' => $this->longText()
            ]);

            $this->createTable(Table::BENETFIT_TYPE_DENTAL, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                'siteId' => $this->integer(),
                //FK
                //intern
                'providerId' => $this->integer(),
                //generic fields
                'internalCode' => $this->string(255)->notNull(),
                'status' => $this->string(255)->notNull(),
                'policyName' => $this->string(255)->notNull(),
                'policyNumber' => $this->string(255)->notNull(),
                'policyHolder' => $this->string(255)->notNull(),
                'content' => $this->longText(),
                'policyStartDate' => $this->dateTime()->notNull(),
                'policyRenewalDate' => $this->dateTime()->notNull(),
                'paymentFrequency' => $this->enum('status', ['annual', 'monthly'])->notNull(),
                'commissionRate' => $this->float()->notNull(),
                'benefitType' => $this->string(255)->notNull()->defaultValue('dental')
            ]);

            $this->createTable(Table::BENETFIT_TYPE_GROUP_CRITICAL_ILLNESS_COVER, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                'siteId' => $this->integer(),
                //FK
                //intern
                'providerId' => $this->integer(),
                //generic fields
                'internalCode' => $this->string(255)->notNull(),
                'status' => $this->string(255)->notNull(),
                'policyName' => $this->string(255)->notNull(),
                'policyNumber' => $this->string(255)->notNull(),
                'policyHolder' => $this->string(255)->notNull(),
                'content' => $this->longText(),
                'policyStartDate' => $this->dateTime()->notNull(),
                'policyRenewalDate' => $this->dateTime()->notNull(),
                'paymentFrequency' => $this->enum('status', ['annual', 'monthly'])->notNull(),
                'commissionRate' => $this->float()->notNull(),
                'benefitType' => $this->string(255)->notNull()->defaultValue('group-critical-illness-cover'),
                //custom fields
                'rateReviewGuaranteeDate' => $this->dateTime(),
                'costingBasis' => $this->enum('costingBasis', ['unit', 'sp']),
                'unitRate' => $this->float(),
                'unitRateSuffix' => $this->enum('unitRateSuffix', ['%', '‰']),
                'freeCoverLevelAutomaticAcceptanceLimit' => $this->float(),
                'dateRefreshFrequency' => $this->enum('dateRefreshFrequency', ['annual', 'monthly'])
            ]);

            $this->createTable(Table::BENETFIT_TYPE_GROUP_DEATH_IN_SERVICE, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                'siteId' => $this->integer(),
                //FK
                //intern
                'providerId' => $this->integer(),
                //generic fields
                'internalCode' => $this->string(255)->notNull(),
                'status' => $this->string(255)->notNull(),
                'policyName' => $this->string(255)->notNull(),
                'policyNumber' => $this->string(255)->notNull(),
                'policyHolder' => $this->string(255)->notNull(),
                'content' => $this->longText(),
                'policyStartDate' => $this->dateTime()->notNull(),
                'policyRenewalDate' => $this->dateTime()->notNull(),
                'paymentFrequency' => $this->enum('status', ['annual', 'monthly'])->notNull(),
                'commissionRate' => $this->float()->notNull(),
                'benefitType' => $this->string(255)->notNull()->defaultValue('group-death-in-service'),
                //custom fields
                'rateReviewGuaranteeDate' => $this->dateTime(),
                'costingBasis' => $this->enum('costingBasis', ['unit', 'sp']),
                'unitRate' => $this->float(),
                'unitRateSuffix' => $this->enum('unitRateSuffix', ['%', '‰']),
                'freeCoverLevelAutomaticAcceptanceLimit' => $this->float(),
                'dateRefreshFrequency' => $this->enum('dateRefreshFrequency', ['annual', 'monthly']),
                'pensionSchemeTaxReferenceNumber' => $this->string(255),
                'dateOfTrustDeed' => $this->dateTime(),
                'eventLimit' => $this->float()
            ]);

            $this->createTable(Table::BENETFIT_TYPE_GROUP_INCOME_PROTECTION, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                'siteId' => $this->integer(),
                //FK
                //intern
                'providerId' => $this->integer(),
                //generic fields
                'internalCode' => $this->string(255)->notNull(),
                'status' => $this->string(255)->notNull(),
                'policyName' => $this->string(255)->notNull(),
                'policyNumber' => $this->string(255)->notNull(),
                'policyHolder' => $this->string(255)->notNull(),
                'content' => $this->longText(),
                'policyStartDate' => $this->dateTime()->notNull(),
                'policyRenewalDate' => $this->dateTime()->notNull(),
                'paymentFrequency' => $this->enum('status', ['annual', 'monthly'])->notNull(),
                'commissionRate' => $this->float()->notNull(),
                'benefitType' => $this->string(255)->notNull()->defaultValue('group-income-protection'),
                //custom fields
                'rateReviewGuaranteeDate' => $this->dateTime(),
                'costingBasis' => $this->enum('costingBasis', ['unit', 'sp']),
                'unitRate' => $this->float(),
                'unitRateSuffix' => $this->enum('unitRateSuffix', ['%', '‰']),
                'freeCoverLevelAutomaticAcceptanceLimit' => $this->float(),
                'dateRefreshFrequency' => $this->enum('dateRefreshFrequency', ['annual', 'monthly'])
            ]);

            $this->createTable(Table::BENETFIT_TYPE_GROUP_LIFE_ASSURANCE, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                'siteId' => $this->integer(),
                //FK
                //intern
                'providerId' => $this->integer(),
                //generic fields
                'internalCode' => $this->string(255)->notNull(),
                'status' => $this->string(255)->notNull(),
                'policyName' => $this->string(255)->notNull(),
                'policyNumber' => $this->string(255)->notNull(),
                'policyHolder' => $this->string(255)->notNull(),
                'content' => $this->longText(),
                'policyStartDate' => $this->dateTime()->notNull(),
                'policyRenewalDate' => $this->dateTime()->notNull(),
                'paymentFrequency' => $this->enum('status', ['annual', 'monthly'])->notNull(),
                'commissionRate' => $this->float()->notNull(),
                'benefitType' => $this->string(255)->notNull()->defaultValue('group-life-assurance'),
                //custom fields
                'rateReviewGuaranteeDate' => $this->dateTime(),
                'costingBasis' => $this->enum('costingBasis', ['unit', 'sp']),
                'unitRate' => $this->float(),
                'unitRateSuffix' => $this->enum('unitRateSuffix', ['%', '‰']),
                'freeCoverLevelAutomaticAcceptanceLimit' => $this->float(),
                'dateRefreshFrequency' => $this->enum('dateRefreshFrequency', ['annual', 'monthly']),
                'pensionSchemeTaxReferenceNumber' => $this->string(255),
                'dateOfTrustDeed' => $this->float(),
                'eventLimit' => $this->float()
            ]);

            $this->createTable(Table::BENETFIT_TYPE_HEALTH_CASH_PLAN, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                'siteId' => $this->integer(),
                //FK
                //intern
                'providerId' => $this->integer(),
                //generic fields
                'internalCode' => $this->string(255)->notNull(),
                'status' => $this->string(255)->notNull(),
                'policyName' => $this->string(255)->notNull(),
                'policyNumber' => $this->string(255)->notNull(),
                'policyHolder' => $this->string(255)->notNull(),
                'content' => $this->longText(),
                'policyStartDate' => $this->dateTime()->notNull(),
                'policyRenewalDate' => $this->dateTime()->notNull(),
                'paymentFrequency' => $this->enum('status', ['annual', 'monthly'])->notNull(),
                'commissionRate' => $this->float()->notNull(),
                'benefitType' => $this->string(255)->notNull()->defaultValue('health-cash-plan'),
            ]);

            $this->createTable(Table::BENETFIT_TYPE_PRIVATE_MEDICAL_INSURANCE, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                'siteId' => $this->integer(),
                //FK
                //intern
                'providerId' => $this->integer(),
                //generic fields
                'internalCode' => $this->string(255)->notNull(),
                'status' => $this->string(255)->notNull(),
                'policyName' => $this->string(255)->notNull(),
                'policyNumber' => $this->string(255)->notNull(),
                'policyHolder' => $this->string(255)->notNull(),
                'content' => $this->longText(),
                'policyStartDate' => $this->dateTime()->notNull(),
                'policyRenewalDate' => $this->dateTime()->notNull(),
                'paymentFrequency' => $this->enum('status', ['annual', 'monthly'])->notNull(),
                'commissionRate' => $this->float()->notNull(),
                'benefitType' => $this->string(255)->notNull()->defaultValue('private-medical-insurance'),
                //custom fields
                'underwritingBasis' => $this->enum('underwritingBasis', ['moratorium', 'medical-history-disregarded', 'full-medical-underwriting']),
                'hospitalList' => $this->string(),
            ]);

            $this->createTable(Table::EMPLOYEES, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                'siteId' => $this->integer(),
                //FK
                //intern
                'employerId' => $this->integer()->notNull(), // create FK to Employer [id]
                'userId' => $this->integer(), // create FK to Craft User [id]
                //fields
                'staffologyId' => $this->string(255)->notNull(),
                'isDirector' => $this->boolean(),
                'status' => $this->enum('status', ['Current', 'Former', 'Upcoming'])->notNull(),
                'aeNotEnroledWarning' => $this->boolean()->defaultValue(0),
                'niNumber' => $this->string(255),
                'sourceSystemId' => $this->string(255),
            ]);

            $this->createTable(Table::EMPLOYERS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                'siteId' => $this->integer(),
                //fields
                'staffologyId' => $this->string(255)->notNull(),
                'name' => $this->string(255)->notNull(),
                'crn' => $this->string(),
                'logoUrl' => $this->string(),
                'alternativeId' => $this->string(),
                'bankPaymentsCsvFormat' => $this->enum('status', ['StandardCsv', 'Telleroo', 'BarclaysBacs', 'SantanderBacs', 'Sif', 'Revolut', 'Standard18FasterPayments', 'Standard18Bacs', 'Bankline', 'BanklineBulk', 'StandardCsvBacs', 'LloydsMultipleStandardCsvBacs', 'LloydsV11CsvBacs', 'CoOpBulkCsvBacs', 'CoOpFasterPaymentsCsv']),
                'bacsServiceUserNumber' => $this->string(),
                'bacsBureauNumber' => $this->string(),
                'rejectInvalidBankDetails' => $this->boolean(),
                'bankPaymentsReferenceFormat' => $this->string(),
                'useTenantRtiSubmissionSettings' => $this->boolean(),
                'employeeCount' => $this->integer(),
                'subcontractorCount' => $this->integer(),
                'startYear' => $this->string(255)->notNull(),
                'currentYear' => $this->string(255)->notNull(),
                'supportAccessEnabled' => $this->boolean(),
                'archived' => $this->boolean(),
                'canUseBureauFeatures' => $this->string(),
                'sourceSystemId' => $this->string(),
                //custom
                'slug' => $this->string(255)->notNull(),
            ]);

            $this->createTable(Table::FAMILY_DETAILS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'employeeId' => $this->integer()->notNull(), // create FK to Employee [id]
                //fields
                'title' => $this->string(255),
                'firstName' => $this->string(255)->notNull(),
                'middleName' => $this->string(255),
                'lastName' => $this->string(255)->notNull(),
                'email' => $this->string(255),
                'telephone' => $this->string(255),
                'mobile' => $this->string(255),
                'gender' => $this->enum('gender', ['Male', 'Female']),
                'niNumber' => $this->string(255),
                'passportNumber' => $this->string(255),
                'relationshipStatus' => $this->enum('relationshipStatus', ['Cohabitate', 'Partner', 'Spouse', 'Child', 'Sibling', 'Parent', 'Other']),
            ]);

            $this->createTable(Table::PAY_RUN_IMPORTS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'payRunId' => $this->integer()->notNull(), // create FK to PayRun [id]
                'uploadedBy' => $this->integer()->notNull(), // create FK to User [id]
                'approvedBy' => $this->integer(), // create FK to User [id]
                //fields
                'filepath' => $this->string(255)->notNull()->defaultValue(''),
                'filename' => $this->string(255)->notNull()->defaultValue(''),
                'rowCount' => $this->integer()->defaultValue(0),
                'status' => $this->enum('status', ['Succeeded', 'Failed']),
                'dateApproved' => $this->dateTime(),
            ]);

            $this->createTable(Table::PAY_RUN, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                'siteId' => $this->integer(),
                //FK
                //intern
                'employerId' => $this->integer()->notNull()->defaultValue(null), // create FK to Employer [id]
                //fields
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
                'state' => $this->string(255)->notNull()->defaultValue(''),
                'isClosed' => $this->boolean()->notNull(),
                'dateClosed' => $this->dateTime(),
                'autoPilotCloseDate' => $this->string(),
                'url' => $this->string()->defaultValue(''),
            ]);

            $this->createTable(Table::PAY_RUN_ENTRIES, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                'siteId' => $this->integer(),
                // FK
                //intern
                'employeeId' => $this->integer()->notNull(),
                'employerId' => $this->integer()->notNull()->defaultValue(null),
                'payRunId' => $this->integer()->notNull()->defaultValue(0),
                // fields
                'staffologyId' => $this->string(255)->notNull(), // staffology: id
                'taxYear' => $this->string(255)->defaultValue(''),
                'startDate' => $this->dateTime(),
                'endDate' => $this->dateTime(),
                'note' => $this->mediumText(),
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
                'forcedCisVatAmount' => $this->double()->defaultValue(0),
                'holidayAccrued' => $this->double()->defaultValue(0),
                'state' => $this->string(255)->defaultValue('Open'),
                'isClosed' => $this->boolean(),
                'manualNi' => $this->boolean(),
                'payrollCodeChanged' => $this->boolean(),
                'aeNotEnroledWarning' => $this->boolean(),
                'receivingOffsetPay' => $this->boolean(),
                'paymentAfterLearning' => $this->boolean(),
                'pdf' => $this->mediumText(),
            ]);

            $this->createTable(Table::PAY_RUN_LOG, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                // FK
                //intern
                'employerId' => $this->integer()->notNull()->defaultValue(0),
                'payRunId' => $this->integer()->notNull()->defaultValue(0),
                // fields
                'employeeCount' => $this->integer()->notNull()->defaultValue(0),
                'taxYear' => $this->string(255)->notNull()->defaultValue(''),
                'lastPeriodNumber' => $this->integer()->notNull()->defaultValue(0),
                'url' => $this->string(255)->notNull()->defaultValue(0),
            ]);

//            $this->createTable(Table::PENSIONS, [
//                'id' => $this->primaryKey(),
//                'dateCreated' => $this->dateTime()->notNull(),
//                'dateUpdated' => $this->dateTime()->notNull(),
//                'uid' => $this->uid(),
//                //FK
//                //intern (also present in staffology call, but needs to be refactored)
//                'employeeId' => $this->integer(), //create FK to Employees [id]
//                'pensionSummaryId' => $this->integer(), //create FK to PensionSummary [id]
//                //fields
//                'staffologyId' => $this->string(), // staffology: id
//                'contributionLevelType' => $this->enum('contributionLevelType', ['UserDefined', 'StatutoryMinimum', 'Nhs2015', 'Tp2020']),
//                'startDate' => $this->string(),
//                'memberReferenceNumber' => $this->string(),
//                'overrideContributions' => $this->boolean(),
//                'employeeContribution' => $this->double(),
//                'employeeContributionIsPercentage' => $this->boolean(),
//                'employerContribution' => $this->double(),
//                'employerContributionIsPercentage' => $this->boolean(),
//                'employerContributionTopUpPercentage' => $this->double(),
//                'isAeQualifyingScheme' => $this->double(),
//                'isTeachersPension' => $this->double(),
//                'aeStatusAtJoining' => $this->enum('contributionLevelType', ['Eligible', 'NonEligible', 'Entitled', 'NoDuties']),
//                'additionalVoluntaryContribution' => $this->double(),
//                'avcIsPercentage' => $this->boolean(),
//                'exitViaProvider' => $this->boolean(),
//                'forceEnrolment' => $this->boolean(),
//                'autoEnrolled' => $this->boolean(),
//            ]);

            $this->createTable(Table::PERMISSIONS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //fields
                'name' => $this->string(255)->notNull()->defaultValue(''),
            ]);

            $this->createTable(Table::PERMISSIONS_USERS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //internal
                'permissionId' => $this->integer()->notNull()->defaultValue(0), //create FK to Permissions [id]
                'userId' => $this->integer()->defaultValue(null), //create FK to User [id]
                'employeeId' => $this->integer()->notNull()->defaultValue(0), //create FK to Employees [id]
            ]);

            $this->createTable(Table::REQUESTS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                'employerId' => $this->integer()->notNull(), //create FK to Employers [id]
                'employeeId' => $this->integer()->notNull(), //create FK to Employees [id]
                'administerId' => $this->integer(), //create FK to User [id]
                //fields
                'dateAdministered' => $this->dateTime(),
                'request' => $this->longText()->notNull(),
                'data' => $this->longText()->notNull(),
                'current' => $this->longText()->notNull(),
                'type' => $this->string()->notNull(),
                'status' => $this->enum('contributionLevelType', ['pending', 'approved', 'declined', 'canceled']),
                'note' => $this->mediumText(),
            ]);

            $this->createTable(Table::HISTORY, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //internal
                'employerId' => $this->integer()->notNull(), //create FK to Employer [id]
                'employeeId' => $this->integer()->notNull(), //create FK to Employee [id]
                'administerId' => $this->integer(), //create FK to Craft User [id] // This can be null
                //fields
                'message' => $this->string(255)->notNull(),
                'data' => $this->longText(),
                'type' => $this->enum('type', ['system', 'payroll', 'pension', 'employee']),
            ]);


            // LINKAGE
            $this->createTable(Table::ADDRESSES, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                'cisSubcontractorId' => $this->integer(), // create FK to CisSubcontractor [id]
                'countryId' => $this->integer(), // create FK to Countries [id]
                'employeeId' => $this->integer(), // create FK to Employers [id]
                'employerId' => $this->integer(), // create FK to Employers [id]
                'pensionAdministratorId' => $this->integer(), // create FK to PensionAdministrator [id]
                'pensionProviderId' => $this->integer(), // create FK to PensionProvider [id]
                'rtiAgentId' => $this->integer(), // create FK to RtiAgent [id]
                'rtiEmployeeAddressId' => $this->integer(), // create FK to RtiEmployeeAddress [id]
                //fields
                'address1' => $this->string(),
                'address2' => $this->string(),
                'address3' => $this->string(),
                'address4' => $this->string(),
                'zipCode' => $this->string(),
            ]);

            $this->createTable(Table::AUTO_ENROLMENT, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'employeeId' => $this->integer(), // create FK to Employee [id]
                //fields
                'state' => $this->enum('state', ['Automatic', 'OptOut', 'OptIn', 'VoluntaryJoiner', 'ContractualPension', 'CeasedMembership', 'Leaver', 'Excluded', 'Enrol']),
                'stateDate' => $this->dateTime(),
                'ukWorker' => $this->enum('status', ['No', 'Yes', 'Ordinarily']),
                'daysToDeferAssessment' => $this->integer(),
                'postponementData' => $this->dateTime(),
                'deferByMonthsNotDays' => $this->boolean(),
                'exempt' => $this->boolean(),
                'aeExclusionCode' => $this->enum('code', ['NotKnown', 'NotAWorker', 'NotWorkingInUk', 'NoOrdinarilyWorkingInUk', 'OutsideOfAgeRange', 'SingleEmployee', 'CeasedActiveMembershipInPast12Mo', 'CeasedActiveMembership', 'ReceivedWulsInPast12Mo', 'ReceivedWuls', 'Leaving', 'TaxProtection', 'CisSubContractor']),
                'aePostponementLetterSent' => $this->boolean(),
            ]);

            $this->createTable(Table::AUTO_ENROLMENT_ASSESSMENT, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //staffology
                'autoEnrolmentId' => $this->integer(), // create FK AutoEnrolment [id]
                //fields
                'staffologyId' => $this->integer(),
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
                'assessmentId' => $this->uid(),
            ]);

            $this->createTable(Table::AUTO_ENROLMENT_ASSESSMENT_ACTION, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'autoEnrolmentAssessmentId' => $this->integer(), //create FK to AutoEnrolmentAssessement [id]
                'pensionSchemeId' => $this->integer(), //create FK to PensionScheme [id]
//                'workerGroupId' => $this->string(),
                //fields
                'action' => $this->enum('status', ['NoChange', 'Enrol', 'Exit', 'Inconclusive', 'Postpone']),
                'employeeState' => $this->enum('state', ['Automatic', 'OptOut', 'OptIn', 'VoluntaryJoiner', 'ContractualPension', 'CeasedMembership', 'Leaver', 'Excluded', 'Enrol']),
                'actionCompleted' => $this->boolean(),
                'actionCompletedMessage' => $this->string(),
                'requiredLetter' => $this->enum('status', ['B1', 'B2', 'B3']),
                'letterNotYetSent' => $this->boolean(),
            ]);

            $this->createTable(Table::AUTO_ENROLMENT_SETTINGS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'employerId' => $this->integer(), //create FK to Employer [id]
                //fields
                'staffologyId' => $this->string(),
                'stagingDate' => $this->string(),
                'cyclicalReenrolmentDate' => $this->string(),
                'previousCyclicalReenrolmentDate' => $this->string(),
                'pensionSameAsDefault' => $this->boolean(),
                'daysToDeferAssessment' => $this->integer(),
                'deferByMonthsNotDays' => $this->boolean(),
                'deferEnrolmentBy' => $this->integer(),
                'deferEnrolmentByPeriodType' => $this->string(),
                'includeNonPensionedEmployeesInSubmission' => $this->boolean(),
            ]);

            $this->createTable(Table::BANK_DETAILS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'employerId' => $this->integer(), //create FK to Employer [id]
                'employeeId' => $this->integer(), //create FK to Employee [id]
                'pensionSchemeId' => $this->integer(), //create FK to PensionScheme [id]
                //fields
                'bankName' => $this->string(),
                'bankBranch' => $this->string(),
                'bankReference' => $this->string(),
                'accountName' => $this->string(),
                'accountNumber' => $this->string(),
                'sortCode' => $this->string(),
                'note' => $this->mediumText(),
            ]);

            $this->createTable(Table::CIS_DETAILS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'cisVerificationDetailsId' => $this->integer(), //create FK to CisVerificationDetails [id]
                'employmentDetailsId' => $this->integer(), //create FK to EmploymentDetails [id]
                //fields
                'type' => $this->enum('type', ['SoleTrader', 'Partnership', 'Company', 'Trust']),
                'utr' => $this->string(),
                'tradingName' => $this->string(),
                'companyUtr' => $this->string(),
                'companyNumber' => $this->string(),
                'vatRegistered' => $this->boolean(),
                'vatNumber' => $this->string(),
                'vatRate' => $this->double(),
                'reverseChargeVAT' => $this->boolean(),
            ]);

            $this->createTable(Table::CIS_PARTNERSHIP, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'cisSubcontractorId' => $this->integer(), //create FK to CisSubcontractor [id]
                //fields
                'name' => $this->string(),
                'utr' => $this->string(),
            ]);

            $this->createTable(Table::CIS_SUBCONTRACTOR, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'cisVerificationDetailsId' => $this->integer(), //create FK to CisVerificationDetails [id]
                //fields
                'employeeUniqueId' => $this->string(),
                'emailStatementTo' => $this->string(),
                'numberOfPayments' => $this->integer(),
                'displayName' => $this->string(),
                'action' => $this->string(),
                'type' => $this->string(),
                'tradingName' => $this->string(),
                'worksRef' => $this->string(),
                'unmatchedRate' => $this->string(),
                'utr' => $this->string(),
                'crn' => $this->string(),
                'nino' => $this->string(),
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
                //FK
                'cisDetailsId' => $this->integer(), // create FK CisDetails [id]
                //fields
                'manuallyEntered' => $this->boolean(),
                'matchInsteadOfVerify' => $this->boolean(),
                'number' => $this->string(),
                'date' => $this->dateTime(),
                'taxStatus' => $this->enum('status', ['Gross', 'NetOfStandardDeduction', 'NotOfHigherDeduction']),
                'verificationRequest' => $this->string(),
            ]);

            $this->createTable(Table::COUNTRIES, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //fields
                'name' => $this->string()->notNull(), //index
                'iso' => $this->string(3)->notNull(),
                'sortOrder' => $this->integer(),
            ]);

            $this->createTable(Table::CUSTOM_PAY_CODES, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'employerId' => $this->integer(), //create FK to Employer [id]
                'payRunEntryId' => $this->integer(), //create FK to PayRunEntry [id]
                'pensionSchemeId' => $this->integer(), //create FK to PensionScheme [id]
                //fields
                'title' => $this->string()->notNull(),
                'code' => $this->string()->notNull(),
                'defaultValue' => $this->string(),
                'isDeduction' => $this->boolean(),
                'isNiable' => $this->boolean(),
                'isTaxable' => $this->boolean(),
                'isPensionable' => $this->boolean(),
                'isAttachable' => $this->boolean(),
                'isRealTimeClass1aNiable' => $this->boolean(),
                'isNotContributingToHolidayPay' => $this->boolean(),
                'isQualifyingEarningsForAe' => $this->boolean(),
                'isNotTierable' => $this->boolean(),
                'isTcp_Tcls' => $this->boolean(),
                'isTcp_Pp' => $this->boolean(),
                'isTcp_Op' => $this->boolean(),
                'isFlexiDd_DeathBenefit' => $this->boolean(),
                'isFlexiDd_Pension' => $this->boolean(),
                'calculationType' => $this->enum('calculationType', ['FixedAmount', 'PercentageOfGross', 'PercentageOfNet', 'MultipleOfHourlyRate']),
                'multiplierType' => $this->enum('multiplierType', ['None', 'Hours', 'Days']),
                'hourlyRateMultiplier' => $this->double(),
                'isSystemCode' => $this->boolean(),
                'isControlCode' => $this->boolean(),
            ]);

            $this->createTable(Table::DEPARTMENT, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'employmentDetailsId' => $this->integer(), // Create FK to EmploymentDetails [id]
                //fields
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
                //FK
                //intern
                'employmentDetailsId' => $this->integer(), // Create FK to EmploymentDetails [id]
                //fields
                'isDirector' => $this->boolean(),
                'startDate' => $this->dateTime(),
                'leaveDate' => $this->dateTime(),
                'niAlternativeMethod' => $this->boolean(),
            ]);

            $this->createTable(Table::EMPLOYER_SETTINGS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'employerId' => $this->integer()->notNull(), // create FK to Employer table [id]
                //fields
                'allowNegativePay' => $this->boolean(),
            ]);

            $this->createTable(Table::EMPLOYMENT_DETAILS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                'employeeId' => $this->integer(), // create FK to Employee [id]
                //fields
                'cisSubContractor' => $this->boolean(),
                'payrollCode' => $this->string(),
                'jobTitle' => $this->string(),
                'onHold' => $this->boolean(),
                'onFurlough' => $this->boolean(),
                'furloughStart' => $this->dateTime(),
                'furloughEnd' => $this->dateTime(),
                'furloughCalculationBasis' => $this->enum('furloughCalculationBasis', ['ActualPaidAmount', 'DailyReferenceAmount', 'MonthlyReferenceAmount']),
                'furloughCalculationBasisAmount' => $this->string(),
                'partialFurlough' => $this->boolean(),
                'furloughHoursNormallyWorked' => $this->double(),
                'furloughHoursOnFurlough' => $this->double(),
                'isApprentice' => $this->boolean(),
                'apprenticeshipStartDate' => $this->dateTime(),
                'apprenticeshipEndDate' => $this->dateTime(),
                'workingPattern' => $this->string(),
                'forcePreviousPayrollCode' => $this->string(),
                // posts are being linked in Item_relations to this id to an item
            ]);

            $this->createTable(Table::FPS_FIELDS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'payRunEntryId' => $this->integer(), //Create FK to PayRunEntries [id]
                'payOptionsId' => $this->integer(), //Create FK to PayOptions [id]
                //fields
                'offPayrollWorker' => $this->boolean(),
                'irregularPaymentPattern' => $this->boolean(),
                'nonIndividual' => $this->boolean(),
                'hoursNormallyWorked' => $this->enum('hours', ['LessThan16', 'MoreThan16', 'MoreThan24', 'MoreThan30', 'NotRegular']),
            ]);

            $this->createTable(Table::HMRC_DETAILS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'employerId' => $this->integer()->notNull(), // create FK to employer [id]
                //fields
                'officeNumber' => $this->string(),
                'payeReference' => $this->string(),
                'accountsOfficeReference' => $this->string(),
                'econ' => $this->string(),
                'utr' => $this->string(),
                'coTax' => $this->string(),
                'employmentAllowance' => $this->boolean(),
                'employmentAllowanceMaxClaim' => $this->string(),
                'smallEmployersRelief' => $this->boolean(),
                'apprenticeshipLevy' => $this->boolean(),
                'apprenticeshipLevyAllowance' => $this->string(),
                'quarterlyPaymentSchedule' => $this->boolean(),
                'includeEmploymentAllowanceOnMonthlyJournal' => $this->boolean(),
                'carryForwardUnpaidLiabilities' => $this->boolean(),
            ]);

            $this->createTable(Table::ITEMS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'autoEnrolmentAssessmentId' => $this->integer(), //create FK to AutoEnrolmentAssesment [id]
                'noteId' => $this->integer(), //create FK to Note [id]
                'cisSubcontractorId' => $this->integer(), //create FK to CisSubcontractor [id]
                //fields
                'staffologyId' => $this->string(),
                'name' => $this->string(),
                'metadata' => $this->longText(),
                'url' => $this->string(),
            ]);

            $this->createTable(Table::ITEM_RELATIONS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'employmentDetailsId' => $this->integer(), //create FK to EmploymentDetails [id]
                'itemId' => $this->integer(), //create FK to Items [id]
                'noteId' => $this->integer(), //create FK to Note [id]
            ]);

            $this->createTable(Table::LEAVER_DETAILS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'employmentDetailsId' => $this->integer(), //create FK in EmploymentDetails [id]
                //fields
                'hasLeft' => $this->boolean(),
                'leaveDate' => $this->dateTime(),
                'isDeceased' => $this->boolean(),
                'paymentAfterLeaving' => $this->boolean(),
                'p45Sent' => $this->boolean(),
            ]);

            $this->createTable(Table::LEAVE_SETTINGS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'employeeId' => $this->integer(), //create FK to Employee [id] //can be null, since leave settings are also for employer
                'employerId' => $this->integer(), //create FK to Employer [id] //can be null, since leave settings are also for employee
                //fields
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
            ]);

            $this->createTable(Table::NATIONAL_INSURANCE_CALCULATION, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'payRunEntryId' => $this->integer(), //create FK to PayRunEntry
                //fields
                'earningsUptoIncludingLEL' => $this->string(),
                'earningsAboveLELUptoIncludingPT' => $this->string(),
                'earningsAbovePTUptoIncludingST' => $this->string(),
                'earningsAbovePTUptoIncludingUEL' => $this->string(),
                'earningsAboveSTUptoIncludingUEL' => $this->string(),
                'earningsAboveUEL' => $this->string(),
                'employeeNiGross' => $this->string(),
                'employeeNiRebate' => $this->string(),
                'employerNiGross' => $this->string(),
                'employerNiRebate' => $this->string(),
                'employeeNi' => $this->string(),
                'employerNi' => $this->string(),
                'netNi' => $this->string(),
            ]);

            $this->createTable(Table::NOTE, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //fields
                'noteDate' => $this->string(),
                'noteText' => $this->string(),
                'createdBy' => $this->string(),
                'updatedBy' => $this->string(),
                'type' => $this->enum('type', ['General', 'RtwProof', 'P45']),
                'documentCount' => $this->integer(),
                //docs are being linked in Item_relations to this id to an item
            ]);

            $this->createTable(Table::OVERSEAS_EMPLOYER_DETAILS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'starterDetailsId' => $this->integer(), //create FK to StarterDetails [id]
                //fields
                'overseasEmployer' => $this->boolean(),
                'overseasSecondmentStatus' => $this->enum('status', ['MoreThan183Days', 'LessThan183Days', 'BothInAndOutOfUK']),
                'eeaCitizen' => $this->boolean(),
                'epm6Scheme' => $this->boolean(),
            ]);

            $this->createTable(Table::PAY_CODES, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                'employerId' => $this->integer(), // Create FK to Employer [id]
                //fields
                'title' => $this->string()->notNull(),
                'code' => $this->string()->notNull(),
                'defaultValue' => $this->double(),
                'isDeduction' => $this->boolean(),
                'isNiable' => $this->boolean(),
                'isTaxable' => $this->boolean(),
                'isPensionable' => $this->boolean(),
                'isAttachable' => $this->boolean(),
                'isRealTimeClass1aNiable' => $this->boolean(),
                'isNotContributingToHolidayPay' => $this->boolean(),
                'isQualifyingEarningsForAe' => $this->boolean(),
                'isNotTierable' => $this->boolean(),
                'isTcp_Tcls' => $this->boolean(),
                'isTcp_Pp' => $this->boolean(),
                'isTcp_Op' => $this->boolean(),
                'isFlexiDd_DeathBenefit' => $this->boolean(),
                'isFlexiDd_Pension' => $this->boolean(),
                'calculationType' => $this->enum('type', ['FixedAmount', 'PercentageOfGross', 'PercentageOfNet', 'MultipleOfHourlyRate']),
                'hourlyRateMultiplier' => $this->double(),
                'isSystemCode' => $this->boolean(),
                'isControlCode' => $this->boolean(),
            ]);

            $this->createTable(Table::PAY_LINES, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'payOptionsId' => $this->integer(), //create FK to PayOptions [id]
//                'pensionId' => $this->integer(), //create FK to Pensions [id]
                //fields
                'value' => $this->string(),
                'rate' => $this->string(),
                'multiplier' => $this->string(),
                'description' => $this->string(),
                'attachmentOrderId' => $this->string(),
                'code' => $this->string(),
            ]);

            $this->createTable(Table::PAY_OPTIONS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'employerId' => $this->integer(), // create FK to Employers [id]
                'employeeId' => $this->integer(), // create FK to Employees [id]
                'payRunEntryId' => $this->integer(), // create FK to PayRunEntries [id]
                //fields
                'period' => $this->enum('period', ['Custom', 'Monthly', 'FourWeekly', 'Fortnightly', 'Weekly', 'Daily']),
                'ordinal' => $this->integer(),
                'payAmount' => $this->string(),
                'basis' => $this->enum('basis', ['Hourly', 'Daily', 'Monthly']),
                'nationalMinimumWage' => $this->boolean(),
                'payAmountMultiplier' => $this->string(),
                'baseHourlyRate' => $this->string(),
                'autoAdjustForLeave' => $this->boolean(),
                'method' => $this->enum('method', ['Cash', 'Cheque', 'Credit', 'DirectDebit']),
                'payCode' => $this->string(),
                'withholdTaxRefundIfPayIsZero' => $this->boolean(),
                'mileageVehicleType' => $this->enum('type', ['Car', 'Motorcycle', 'Cycle']),
                'mapsMiles' => $this->integer(),
            ]);

            $this->createTable(Table::PAY_RUN_TOTALS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                // FK
                //internal
                'payRunId' => $this->integer(), // Create FK to PayRun [id]
                'payRunEntryId' => $this->integer(), // Create FK to PayRunEntries [id]
                // fields
                'basicPay' => $this->string(),
                'gross' => $this->string(),
                'grossForNi' => $this->string(),
                'grossNotSubjectToEmployersNi' => $this->string(),
                'grossForTax' => $this->string(),
                'employerNi' => $this->string(),
                'employeeNi' => $this->string(),
                'employerNiOffPayroll' => $this->double(),
                'realTimeClass1ANi' => $this->double(),
                'tax' => $this->string(),
                'netPay' => $this->string(),
                'adjustments' => $this->string(),
                'additions' => $this->string(),
                'deductions' => $this->string(),
                'takeHomePay' => $this->string(),
                'nonTaxOrNICPmt' => $this->string(),
                'itemsSubjectToClass1NIC' => $this->double(),
                'dednsFromNetPay' => $this->double(),
                'tcp_Tcls' => $this->double(),
                'tcp_Pp' => $this->double(),
                'tcp_Op' => $this->double(),
                'flexiDd_Death' => $this->double(),
                'flexiDd_Death_NonTax' => $this->double(),
                'flexiDd_Pension' => $this->double(),
                'flexiDd_Pension_NonTax' => $this->double(),
                'smp' => $this->double(),
                'spp' => $this->double(),
                'sap' => $this->double(),
                'shpp' => $this->double(),
                'spbp' => $this->double(),
                'ssp' => $this->double(),
                'studentLoanRecovered' => $this->string(),
                'postgradLoanRecovered' => $this->string(),
                'pensionableEarnings' => $this->string(),
                'pensionablePay' => $this->string(),
                'nonTierablePay' => $this->string(),
                'employeePensionContribution' => $this->string(),
                'employeePensionContributionAvc' => $this->string(),
                'employerPensionContribution' => $this->string(),
                'empeePenContribnsNotPaid' => $this->string(),
                'empeePenContribnsPaid' => $this->string(),
                'attachmentOrderDeductions' => $this->string(),
                'cisDeduction' => $this->string(),
                'cisVat' => $this->string(),
                'cisUmbrellaFee' => $this->string(),
                'cisUmbrellaFeePostTax' => $this->string(),
                'pbik' => $this->double(),
                'mapsMiles' => $this->integer(),
                'umbrellaFee' => $this->string(),
                'appLevyDeduction' => $this->double(),
                'paymentAfterLeaving' => $this->double(),
                'taxOnPaymentAfterLeaving' => $this->double(),
                'nilPaid' => $this->integer(),
                'leavers' => $this->integer(),
                'starters' => $this->integer(),
                'totalCost' => $this->string(),
                'isYtd' => $this->boolean(),
            ]);

            $this->createTable(Table::PENSION_ADMINISTRATOR, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'pensionSchemeId' => $this->integer(), //create FK to PensionScheme [id]
                //fields
                'staffologyId' => $this->string(),
                'name' => $this->string(),
                'email' => $this->string(),
                'telephone' => $this->string(),
            ]);

            $this->createTable(Table::PENSIONER_PAYROLL, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'starterDetailsId' => $this->integer(), //create FK to StarterDetails [id]
                //fields
                'inReceiptOfPension' => $this->boolean(),
                'bereaved' => $this->boolean(),
                'amount' => $this->double(),
            ]);

//            $this->createTable(Table::PENSION_PROVIDER, [
//                'id' => $this->primaryKey(),
//                'dateCreated' => $this->dateTime()->notNull(),
//                'dateUpdated' => $this->dateTime()->notNull(),
//                'uid' => $this->uid(),
//                //FK
//                //intern
//                'pensionSchemeId' => $this->integer(), //create FK to PensionScheme [id]
//                //fields
//                'staffologyId' => $this->string(),
//                'name' => $this->string()->notNull(),
//                'accountNo' => $this->string(),
//                'portal' => $this->string(),
//                'website' => $this->string(),
//                'telephone' => $this->string(),
//                'papdisVersion' => $this->enum('papdisVersion', ['PAP10', 'PAP11']),
//                'papdisProviderId' => $this->string(),
//                'papdisEmployerId' => $this->string(),
//                'csvFormat' => $this->enum('csvFormat', ['Papdis', 'Nest', 'NowPensions', 'TeachersPensionMdc', 'TeachersPensionMcr']),
//                'excludeNilPaidFromContributions' => $this->boolean(),
//                'payPeriodDateAdjustment' => $this->integer(),
//                'miscBoolean1' => $this->boolean(),
//                'miscBoolean2' => $this->boolean(),
//                'miscString1' => $this->string(),
//                'miscString2' => $this->string(),
//                'optOutWindow' => $this->integer(),
//                'optOutWindowIsMonths' => $this->boolean(),
//            ]);

//            $this->createTable(Table::PENSION_SCHEME, [
//                'id' => $this->primaryKey(),
//                'dateCreated' => $this->dateTime()->notNull(),
//                'dateUpdated' => $this->dateTime()->notNull(),
//                'uid' => $this->uid(),
//                //FK
//                //intern
//                'employerId' => $this->integer(), // create FK to Employer [id]
//                'pensionId' => $this->integer(), // create FK to Pension [id]
//                'pensionSummaryId' => $this->integer(), // create FK to PensionSummaryId [id]
//                //fields
//                'staffologyId' => $this->string(),
//                'name' => $this->string()->notNull(),
//                'pensionRule' => $this->enum('period', ['ReliefAtSource', 'SalarySacrifice', 'NetPayArrangement']),
//                'qualifyingScheme' => $this->boolean(),
//                'disableAeLetters' => $this->boolean(),
//                'subtractBasicRateTax' => $this->boolean(),
//                'payMethod' => $this->enum('period', ['Cash', 'Cheque', 'Credit', 'DirectDebit']),
//                'useCustomPayCodes' => $this->boolean(),
//            ]);

//            $this->createTable(Table::PENSION_SELECTION, [
//                'id' => $this->primaryKey(),
//                'dateCreated' => $this->dateTime()->notNull(),
//                'dateUpdated' => $this->dateTime()->notNull(),
//                'uid' => $this->uid(),
//                //FK
//                //intern
//                'autoEnrollmentSettingsId' => $this->integer(), // create FK to AutoEnrollmentSettings [id]
//                'employerId' => $this->integer(), // create FK to Employer [id]
//                'pensionSchemeId' => $this->integer(), // create FK to PensionSchema [id] //refactored from Staffology to link
////                'workerGroupId' => $this->string(), // create FK to WorkerGroup [id]
//                //fields
//                'staffologyId' => $this->integer()
//            ]);

            $this->createTable(Table::PENSION_SUMMARY, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'payRunEntryId' => $this->integer(), // create FK to PensionSummary [id]
                'workerGroupId' => $this->integer(), // create FK to WorkerGroup [id]
//                'pensionId' => $this->integer(), // create FK to PensionSchema [id]
//                'pensionSchemeId' => $this->integer(), // create FK to PensionSchema [id]
                //fields
                'name' => $this->string(),
                'startDate' => $this->string(),
                'pensionRule' => $this->enum('pensionRule', ['ReliefAtSource', 'SalarySacrifice', 'NetPayArrangement'])->notNull(),
                'employeePensionContributionMultiplier' => $this->string(),
                'additionalVoluntaryContribution' => $this->string(),
                'avcIsPercentage' => $this->boolean(),
                'autoEnrolled' => $this->boolean(),
                'papdisPensionProviderId' => $this->integer(),
                'papdisEmployerId' => $this->integer()
            ]);

            $this->createTable(Table::PERSONAL_DETAILS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'employeeId' => $this->integer(), // create FK to Employees [id]
                //fields
                'maritalStatus' => $this->enum('maritalStatus', ['Single', 'Married', 'Divorced', 'Widowed', 'CivilPartnership', 'Unknown'])->notNull(),
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
                'dateOfBirth' => $this->dateTime()->notNull(),
                'statePensionAge' => $this->integer()->notNull(),
                'gender' => $this->enum('gender', ['Male', 'Female'])->notNull(),
                'niNumber' => $this->string(255)->notNull(),
                'passportNumber' => $this->string(255)->notNull(),
            ]);

            $this->createTable(Table::RIGHT_TO_WORK, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                'employeeId' => $this->integer(), //create FK to Employee table [id]
                //fields
                'checked' => $this->boolean(),
                'documentType' => $this->enum('type', ['Other', 'Visa', 'Passport', 'BirthCertificate', 'IdentityCard', 'Sharecode']),
                'documentRef' => $this->string(),
                'documentExpiry' => $this->dateTime(),
                'note' => $this->mediumText(),
            ]);

            $this->createTable(Table::RTI_AGENT, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'rtiSubmissionSettingsId' => $this->integer(), //create FK to rtiSubmissionSettings [id]
                //fields
                'agentId' => $this->string(),
                'company' => $this->string(),
                'email' => $this->string(),
                'telephone' => $this->string(),
            ]);

            $this->createTable(Table::RTI_CONTACT, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'rtiSubmissionSettingsId' => $this->integer(), //create FK to rtiSubmissionSettings [id]
                //fields
                'firstName' => $this->string(),
                'lastName' => $this->string(),
                'email' => $this->string(),
                'telephone' => $this->string(),
            ]);

            $this->createTable(Table::RTI_EMPLOYEE_ADDRESS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'employeeId' => $this->integer(), // create FK to Employee [id]
                //fields
                'postcode_v1' => $this->string(), //staffology api call --> postcode
                'postcode_v2' => $this->string(), //staffology api call --> postCode
                'ukPostcode' => $this->string(),
            ]);

            $this->createTable(Table::RTI_EMPLOYEE_NAME, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'cisSubcontractorId' => $this->integer(), //create FK to CisSubcontractor [id]
                //fields
                'ttl' => $this->string(),
                'fore' => $this->longText(),
                'initials' => $this->string(),
                'sur' => $this->string(),
            ]);

            $this->createTable(Table::RTI_SUBMISSION_SETTINGS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'employerId' => $this->integer(), // create FK to Employer [id]
                //fields
                'senderType' => $this->enum('declaration', ['ActingInCapacity', 'Agent', 'Bureau', 'Company', 'Employer', 'Government', 'Individual', 'Other', 'Partnership', 'Trust']),
                'senderId' => $this->string(),
                'password' => $this->string(),
                'excludeNilPaid' => $this->boolean(),
                'includeHashCrossRef' => $this->boolean(),
                'autoSubmitFps' => $this->boolean(),
                'testInLive' => $this->boolean(),
                'useTestGateway' => $this->boolean(),
                'overrideTimestampValue' => $this->string(),
            ]);

            $this->createTable(Table::STARTER_DETAILS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'employmentDetailsId' => $this->integer(), // create FK to employmentDetails [id]
                //fields
                'startDate' => $this->dateTime()->notNull(),
                'starterDeclaration' => $this->enum('declaration', ['A', 'B', 'C', 'Unknown'])->notNull(),
            ]);

            $this->createTable(Table::TAX_AND_NI, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'payOptionsId' => $this->integer(), //create FK to PayOptions [id]
                //fields
                'niTable' => $this->string()->notNull(),
                'secondaryClass1NotPayable' => $this->boolean(),
                'postgradLoan' => $this->boolean(),
                'postgraduateLoanStartDate' => $this->dateTime(),
                'postgraduateLoanEndDate' => $this->dateTime(),
                'studentLoan' => $this->boolean(),
                'studentLoanStartDate' => $this->dateTime(),
                'studentLoanEndDate' => $this->dateTime(),
                'taxCode' => $this->string(),
                'week1Month1' => $this->boolean(),
            ]);

//            $this->createTable(Table::TEACHER_PENSION_DETAILS, [
//                'id' => $this->primaryKey(),
//                'dateCreated' => $this->dateTime()->notNull(),
//                'dateUpdated' => $this->dateTime()->notNull(),
//                'uid' => $this->uid(),
//                //FK
//                //intern
//                'pensionId' => $this->integer(), //create FK to Pensions [id]
//                //fields
//                'employmentType' => $this->enum('type', ['FullTime', 'PartTimeRegular', 'IrregularPartTime', 'IrregularPartTime_In']),
//                'fullTimeSalary' => $this->integer(),
//                'partTimeSalaryPaid' => $this->integer(),
//            ]);

//            $this->createTable(Table::TIER_PENSION_RATE, [
//                'id' => $this->primaryKey(),
//                'dateCreated' => $this->dateTime()->notNull(),
//                'dateUpdated' => $this->dateTime()->notNull(),
//                'uid' => $this->uid(),
//                //FK
//                //intern
//                'pensionId' => $this->integer(), //create FK to Pensions [id]
//                //fields
//                'name' => $this->string(),
//                'description' => $this->string(),
//                'rangeStart' => $this->double(),
//                'rate' => $this->double(),
//            ]);

            $this->createTable(Table::UMBRELLA_PAYMENT, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'payRunEntryId' => $this->integer(), //create FK to PayRunEntry [id]
                // fields
                'payrollCode' => $this->string(),
                'chargePerTimesheet' => $this->double(),
                'invoiceValue' => $this->double(),
                'mapsMiles' => $this->integer(),
                'otherExpenses' => $this->double(),
                'numberOfTimesheets' => $this->integer(),
                'hoursWorked' => $this->double(),
                'grossDeduction' => $this->double(),
                'grossAddition' => $this->double(),
            ]);

            $this->createTable(Table::UMBRELLA_SETTINGS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'employerId' => $this->integer(), // create FK to Employer [id]
                // fields
                'enabled' => $this->boolean(),
                'chargePerTimesheet' => $this->double(),
                'apprenticeshipLevyDednRate' => $this->double(),
                'holidayRate' => $this->double(),
                'dpsbCode' => $this->string(),
                'expensesCode' => $this->string(),
                'grossDeductionCode' => $this->string(),
                'holidayCode' => $this->string(),
                'cisFeeCode' => $this->string(),
                'detailFeeInComment' => $this->boolean(),
            ]);

            $this->createTable(Table::VALUE_OVERRIDE, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'payRunEntryId' => $this->integer(), // create FK to PayRunEntry table [id]
                // fields
                'type' => $this->enum('type', ['BasicPay', 'Gross', 'GrossForTax', 'GrossForNi', 'EmployerNi', 'EmployeeNi', 'EmployerNiOffPayroll', 'RealTimeClass1ANi', 'Tax', 'NetPay', 'Adjustments', 'TakeHomePay', 'NonTaxOrNICPmt', 'ItemsSubjectToClass1NIC', 'DednsFromNetPay', 'Tcp_Tcls', 'Tcp_Pp', 'Tcp_Op', 'FlexiDd_Death', 'FlexiDd_Death_NonTax', 'FlexiDd_Pension', 'FlexiDd_Pension_NonTax', 'Smp', 'Spp', 'Sap', 'Shpp', 'Spbp', 'StudentLoanRecovered', 'PostgradLoanRecovered', 'PensionablePay', 'NonTierablePay', 'EmployeePensionContribution', 'EmployerPensionContribution', 'EmpeePenContribnsNotPaid', 'EmpeePenContribnsPaid', 'AttachmentOrderDeductions', 'CisDeduction', 'CisVat', 'CisUmbrellaFee', 'CisUmbrellaFeePostTax', 'Pbik', 'MapsMiles', 'UmbrellaFee', 'AppLevyDeduction', 'PaymentAfterLeaving', 'TaxOnPaymentAfterLeaving', 'Ssp', 'AttachmentOrderAdminFee', 'EmployeePensionNetPay', 'EmployeePensionRas', 'EmployeePensionSalSac', 'EmployeePensionContributionAvc', 'Deductions', 'Additions', 'PensionableEarnings']),
                'value' => $this->double(),
                'originalValue' => $this->double(),
                'note' => $this->mediumText(),
                'attachmentOrderId' => $this->string(),
                // custom
                'field' => $this->enum('type', ['periodOverrides', 'totalsYtdOverrides']),
            ]);

            $this->createTable(Table::WORKER_GROUP, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
//                'pensionId' => $this->integer(), // create FK to Pensions [id]
//                'pensionSchemeId' => $this->integer(), // create FK to PensionScheme [id]
                // fields
                'staffologyId' => $this->string(), // staffology: id
                'name' => $this->string()->notNull(),
                'contributionLevelType' => $this->enum('type', ['UserDefined', 'StatutoryMinimum', 'Nhs2015', 'Tp2020']),
                'employeeContribution' => $this->string(),
                'employeeContributionIsPercentage' => $this->boolean(),
                'employerContribution' => $this->string(),
                'employerContributionIsPercentage' => $this->boolean(),
                'employerContributionTopUpPercentage' => $this->string(),
                'customThreshold' => $this->boolean(),
                'lowerLimit' => $this->string(),
                'upperLimit' => $this->string(),
                'papdisGroup' => $this->string(),
                'papdisSubGroup' => $this->string(),
                'localAuthorityNumber' => $this->string(),
                'schoolEmployerType' => $this->string(),
                'workerGroupId' => $this->string(),
            ]);

            $tableCreated = true;
        }


        return $tableCreated;
    }


    /**
     * Creates the indexes
     */
    public function createIndexes(): void
    {
        /** BASE **/
        // Benefit Providers [name]
        $this->createIndex(null, Table::BENEFIT_PROVIDERS, 'name', true);

        // Benefit Types [benefitType]
        $this->createIndex(null, Table::BENETFIT_TYPE_DENTAL, 'benefitType', true);
        $this->createIndex(null, Table::BENETFIT_TYPE_GROUP_CRITICAL_ILLNESS_COVER, 'benefitType', true);
        $this->createIndex(null, Table::BENETFIT_TYPE_GROUP_DEATH_IN_SERVICE, 'benefitType', true);
        $this->createIndex(null, Table::BENETFIT_TYPE_GROUP_INCOME_PROTECTION, 'benefitType', true);
        $this->createIndex(null, Table::BENETFIT_TYPE_GROUP_LIFE_ASSURANCE, 'benefitType', true);
        $this->createIndex(null, Table::BENETFIT_TYPE_HEALTH_CASH_PLAN, 'benefitType', true);
        $this->createIndex(null, Table::BENETFIT_TYPE_PRIVATE_MEDICAL_INSURANCE, 'benefitType', true);

        // Benefit Types [internalCode]
        $this->createIndex(null, Table::BENETFIT_TYPE_DENTAL, 'internalCode', true);
        $this->createIndex(null, Table::BENETFIT_TYPE_GROUP_CRITICAL_ILLNESS_COVER, 'internalCode', true);
        $this->createIndex(null, Table::BENETFIT_TYPE_GROUP_DEATH_IN_SERVICE, 'internalCode', true);
        $this->createIndex(null, Table::BENETFIT_TYPE_GROUP_INCOME_PROTECTION, 'internalCode', true);
        $this->createIndex(null, Table::BENETFIT_TYPE_GROUP_LIFE_ASSURANCE, 'internalCode', true);
        $this->createIndex(null, Table::BENETFIT_TYPE_HEALTH_CASH_PLAN, 'internalCode', true);
        $this->createIndex(null, Table::BENETFIT_TYPE_PRIVATE_MEDICAL_INSURANCE, 'internalCode', true);

        // Employees [id]
        $this->createIndex(null, Table::AUTO_ENROLMENT, 'employeeId', false);
        $this->createIndex(null, Table::ADDRESSES, 'employeeId', true);
        $this->createIndex(null, Table::BANK_DETAILS, 'employeeId', true);
        $this->createIndex(null, Table::EMPLOYMENT_DETAILS, 'employeeId', true);
        $this->createIndex(null, Table::FAMILY_DETAILS, 'employeeId', false);
        $this->createIndex(null, Table::HISTORY, 'employeeId', false);
        $this->createIndex(null, Table::LEAVE_SETTINGS, 'employeeId', false);
        $this->createIndex(null, Table::PAY_OPTIONS, 'employeeId', false);
        $this->createIndex(null, Table::PAY_RUN_ENTRIES, 'employeeId', false);
//        $this->createIndex(null, Table::PENSIONS, 'employeeId', false);
        $this->createIndex(null, Table::PERMISSIONS_USERS, 'employeeId', false);
        $this->createIndex(null, Table::PERSONAL_DETAILS, 'employeeId', true);
        $this->createIndex(null, Table::REQUESTS, 'employeeId', false);
        $this->createIndex(null, Table::RIGHT_TO_WORK, 'employeeId', false);
        $this->createIndex(null, Table::RTI_EMPLOYEE_ADDRESS, 'employeeId', false);

        //Employer [id]
        $this->createIndex(null, Table::ADDRESSES, 'employerId', true);
        $this->createIndex(null, Table::AUTO_ENROLMENT_SETTINGS, 'employerId', false);
        $this->createIndex(null, Table::BANK_DETAILS, 'employerId', true);
        $this->createIndex(null, Table::CUSTOM_PAY_CODES, 'employerId', false);
        $this->createIndex(null, Table::EMPLOYEES, 'employerId', false);
        $this->createIndex(null, Table::EMPLOYER_SETTINGS, 'employerId', false);
        $this->createIndex(null, Table::HISTORY, 'employerId', false);
        $this->createIndex(null, Table::HMRC_DETAILS, 'employerId', true);
        $this->createIndex(null, Table::LEAVE_SETTINGS, 'employerId', false);
        $this->createIndex(null, Table::PAY_CODES, 'employerId', false);
        $this->createIndex(null, Table::PAY_OPTIONS, 'employerId', false);
        $this->createIndex(null, Table::PAY_RUN, 'employerId', false);
        $this->createIndex(null, Table::PAY_RUN_ENTRIES, 'employerId', false);
        $this->createIndex(null, Table::PAY_RUN_LOG, 'employerId', false);
//        $this->createIndex(null, Table::PENSION_SCHEME, 'employerId', false);
//        $this->createIndex(null, Table::PENSION_SELECTION, 'employerId', false);
        $this->createIndex(null, Table::REQUESTS, 'employerId', false);
        $this->createIndex(null, Table::RTI_SUBMISSION_SETTINGS, 'employerId', false);
        $this->createIndex(null, Table::UMBRELLA_SETTINGS, 'employerId', false);

        //Country [id]
        $this->createIndex(null, Table::ADDRESSES, 'countryId', false);

        //Pay Run [id]
        $this->createIndex(null, Table::PAY_RUN_TOTALS, 'payRunId', false);
        $this->createIndex(null, Table::PAY_RUN_ENTRIES, 'payRunId', false);
        $this->createIndex(null, Table::PAY_RUN_IMPORTS, 'payRunId', false);
        $this->createIndex(null, Table::PAY_RUN_LOG, 'payRunId', false);

        //Pay Run Entries [id]
        $this->createIndex(null, Table::CUSTOM_PAY_CODES, 'payRunEntryId', false);
        $this->createIndex(null, Table::FPS_FIELDS, 'payRunEntryId', false);
        $this->createIndex(null, Table::NATIONAL_INSURANCE_CALCULATION, 'payRunEntryId', false);
        $this->createIndex(null, Table::PAY_OPTIONS, 'payRunEntryId', true);
        $this->createIndex(null, Table::PAY_RUN_TOTALS, 'payRunEntryId', false);
        $this->createIndex(null, Table::PENSION_SUMMARY, 'payRunEntryId', false);
        $this->createIndex(null, Table::UMBRELLA_PAYMENT, 'payRunEntryId', false);
        $this->createIndex(null, Table::VALUE_OVERRIDE, 'payRunEntryId', false);

        // Request
        $this->createIndex(null, Table::REQUESTS, 'status', false);

        //Pensions [id]
//        $this->createIndex(null, Table::PAY_LINES, 'pensionId', false);
//        $this->createIndex(null, Table::PENSION_SCHEME, 'pensionId', false);
//        $this->createIndex(null, Table::PENSION_SUMMARY, 'pensionId', false);
//        $this->createIndex(null, Table::TEACHER_PENSION_DETAILS, 'pensionId', false);
//        $this->createIndex(null, Table::TIER_PENSION_RATE, 'pensionId', false);
//        $this->createIndex(null, Table::WORKER_GROUP, 'pensionId', false);

        //Permissions [id]
        $this->createIndex(null, Table::PERMISSIONS_USERS, 'permissionId', false);

        /** LINKAGE **/
        //Employment Details [id]
        $this->createIndex(null, Table::CIS_DETAILS, 'employmentDetailsId', false);
        $this->createIndex(null, Table::DEPARTMENT, 'employmentDetailsId', false);
        $this->createIndex(null, Table::DIRECTORSHIP_DETAILS, 'employmentDetailsId', false);
        $this->createIndex(null, Table::ITEM_RELATIONS, 'employmentDetailsId', false);
        $this->createIndex(null, Table::LEAVER_DETAILS, 'employmentDetailsId', false);
        $this->createIndex(null, Table::STARTER_DETAILS, 'employmentDetailsId', false);

        //Auto Enrolment [id]
        $this->createIndex(null, Table::AUTO_ENROLMENT_ASSESSMENT, 'autoEnrolmentId', false);

        //Auto Enrolment Assessment [id]
        $this->createIndex(null, Table::AUTO_ENROLMENT_ASSESSMENT_ACTION, 'autoEnrolmentAssessmentId', false);
        $this->createIndex(null, Table::ITEMS, 'autoEnrolmentAssessmentId', false);

        //Auto Enrolment Settings [id]
//        $this->createIndex(null, Table::PENSION_SELECTION, 'autoEnrollmentSettingsId', false);

        //Cis Subcontractor [id]
        $this->createIndex(null, Table::ADDRESSES, 'cisSubcontractorId', false);
        $this->createIndex(null, Table::CIS_PARTNERSHIP, 'cisSubcontractorId', false);
        $this->createIndex(null, Table::ITEMS, 'cisSubcontractorId', false);
        $this->createIndex(null, Table::RTI_EMPLOYEE_NAME, 'cisSubcontractorId', false);

        //Cis Verification Details [id]
        $this->createIndex(null, Table::CIS_DETAILS, 'cisVerificationDetailsId', false);
        $this->createIndex(null, Table::CIS_SUBCONTRACTOR, 'cisVerificationDetailsId', false);

        //Note [id]
        $this->createIndex(null, Table::ITEMS, 'noteId', false);
        $this->createIndex(null, Table::ITEM_RELATIONS, 'noteId', false);

        //Item [id]
        $this->createIndex(null, Table::ITEM_RELATIONS, 'itemId', false);

        //Pay Options [id]
        $this->createIndex(null, Table::FPS_FIELDS, 'payOptionsId', false);
        $this->createIndex(null, Table::PAY_LINES, 'payOptionsId', false);
        $this->createIndex(null, Table::TAX_AND_NI, 'payOptionsId', false);

        //Pension Administrator [id]
        $this->createIndex(null, Table::ADDRESSES, 'pensionAdministratorId', false);

        //Pension Provider [id]
//        $this->createIndex(null, Table::ADDRESSES, 'pensionProviderId', true);

        //Pension Scheme [id]
//        $this->createIndex(null, Table::AUTO_ENROLMENT_ASSESSMENT_ACTION, 'pensionSchemeId', false);
//        $this->createIndex(null, Table::BANK_DETAILS, 'pensionSchemeId', false);
//        $this->createIndex(null, Table::CUSTOM_PAY_CODES, 'pensionSchemeId', false);
//        $this->createIndex(null, Table::PENSION_ADMINISTRATOR, 'pensionSchemeId', false);
//        $this->createIndex(null, Table::PENSION_PROVIDER, 'pensionSchemeId', false);
//        $this->createIndex(null, Table::PENSION_SELECTION, 'pensionSchemeId', false);
//        $this->createIndex(null, Table::WORKER_GROUP, 'pensionSchemeId', false);

        //Pension Summary [id]
//        $this->createIndex(null, Table::PENSIONS, 'pensionSummaryId', false);
//        $this->createIndex(null, Table::PENSION_SCHEME, 'pensionSummaryId', false);

        //Rti Agent [id]
        $this->createIndex(null, Table::ADDRESSES, 'rtiAgentId', false);

        //Rti Employee Address [id]
        $this->createIndex(null, Table::ADDRESSES, 'rtiEmployeeAddressId', false);

        //Rti Submission Settings [id]
        $this->createIndex(null, Table::RTI_AGENT, 'rtiSubmissionSettingsId', false);
        $this->createIndex(null, Table::RTI_CONTACT, 'rtiSubmissionSettingsId', false);


        //Starter Details [id]
        $this->createIndex(null, Table::OVERSEAS_EMPLOYER_DETAILS, 'starterDetailsId', false);
        $this->createIndex(null, Table::PENSIONER_PAYROLL, 'starterDetailsId', false);

        //Worker Group [id]
        $this->createIndex(null, Table::PENSION_SUMMARY, 'workerGroupId', false);

        /** CRAFT **/
        // User [id]
        $this->createIndex(null, Table::EMPLOYEES, 'userId', false);
        $this->createIndex(null, Table::HISTORY, 'administerId', false);
        $this->createIndex(null, Table::PAY_RUN_IMPORTS, 'approvedBy', false);
        $this->createIndex(null, Table::PAY_RUN_IMPORTS, 'uploadedBy', false);
        $this->createIndex(null, Table::PERMISSIONS_USERS, 'userId', false);
        $this->createIndex(null, Table::REQUESTS, 'administerId', false);

        /** STAFFOLOGY **/
        $this->createIndex(null, Table::EMPLOYEES, 'staffologyId', true);
        $this->createIndex(null, Table::EMPLOYEES, 'staffologyId', true);
//        $this->createIndex(null, Table::PENSIONS, 'staffologyId', true);
        $this->createIndex(null, Table::PAY_RUN_ENTRIES, 'staffologyId', true);
        $this->createIndex(null, Table::ITEMS, 'staffologyId', true);
//        $this->createIndex(null, Table::PENSION_ADMINISTRATOR, 'staffologyId', true);
//        $this->createIndex(null, Table::PENSION_PROVIDER, 'staffologyId', true);
//        $this->createIndex(null, Table::PENSION_SCHEME, 'staffologyId', true);
//        $this->createIndex(null, Table::PENSION_SELECTION, 'staffologyId', true);
        $this->createIndex(null, Table::WORKER_GROUP, 'staffologyId', false);
    }

    /**
     * Creates the foreign keys needed for the Records used by the plugin
     *
     * @return void
     */
    protected function addForeignKeys()
    {
        /** BASE **/
        // Benefit Providers [id]
        $this->addForeignKey(null, Table::BENETFIT_TYPE_DENTAL, ['providerId'], Table::BENEFIT_PROVIDERS, ['id'], 'CASCADE', 'CASCADE' );
        $this->addForeignKey(null, Table::BENETFIT_TYPE_GROUP_CRITICAL_ILLNESS_COVER, ['providerId'], Table::BENEFIT_PROVIDERS, ['id'], 'CASCADE', 'CASCADE' );
        $this->addForeignKey(null, Table::BENETFIT_TYPE_GROUP_DEATH_IN_SERVICE, ['providerId'], Table::BENEFIT_PROVIDERS, ['id'], 'CASCADE', 'CASCADE' );
        $this->addForeignKey(null, Table::BENETFIT_TYPE_GROUP_INCOME_PROTECTION, ['providerId'], Table::BENEFIT_PROVIDERS, ['id'], 'CASCADE', 'CASCADE' );
        $this->addForeignKey(null, Table::BENETFIT_TYPE_GROUP_LIFE_ASSURANCE, ['providerId'], Table::BENEFIT_PROVIDERS, ['id'], 'CASCADE', 'CASCADE' );
        $this->addForeignKey(null, Table::BENETFIT_TYPE_HEALTH_CASH_PLAN, ['providerId'], Table::BENEFIT_PROVIDERS, ['id'], 'CASCADE', 'CASCADE' );
        $this->addForeignKey(null, Table::BENETFIT_TYPE_PRIVATE_MEDICAL_INSURANCE, ['providerId'], Table::BENEFIT_PROVIDERS, ['id'], 'CASCADE', 'CASCADE' );

        // Employees [id]
        $this->addForeignKey(null, Table::AUTO_ENROLMENT, ['employeeId'], Table::EMPLOYEES, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::ADDRESSES, ['employeeId'], Table::EMPLOYEES, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::BANK_DETAILS, ['employeeId'], Table::EMPLOYEES, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::EMPLOYMENT_DETAILS, ['employeeId'], Table::EMPLOYEES, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::FAMILY_DETAILS, ['employeeId'], Table::EMPLOYEES, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::HISTORY, ['employeeId'], Table::EMPLOYEES, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::LEAVE_SETTINGS, ['employeeId'], Table::EMPLOYEES, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::PAY_OPTIONS, ['employeeId'], Table::EMPLOYEES, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::PAY_RUN_ENTRIES, ['employeeId'], Table::EMPLOYEES, ['id'], 'CASCADE', 'CASCADE');
//        $this->addForeignKey(null, Table::PENSIONS, ['employeeId'], Table::EMPLOYEES, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::PERMISSIONS_USERS, ['employeeId'], Table::EMPLOYEES, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::PERSONAL_DETAILS, ['employeeId'], Table::EMPLOYEES, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::REQUESTS, ['employeeId'], Table::EMPLOYEES, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::RIGHT_TO_WORK, ['employeeId'], Table::EMPLOYEES, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::RTI_EMPLOYEE_ADDRESS, ['employeeId'], Table::EMPLOYEES, ['id'], 'CASCADE', 'CASCADE');

        //Employer [id]
        $this->addForeignKey(null, Table::ADDRESSES, ['employerId'], Table::EMPLOYERS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::AUTO_ENROLMENT_SETTINGS, ['employerId'], Table::EMPLOYERS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::BANK_DETAILS, ['employerId'], Table::EMPLOYERS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::CUSTOM_PAY_CODES, ['employerId'], Table::EMPLOYERS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::EMPLOYEES, ['employerId'], Table::EMPLOYERS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::EMPLOYER_SETTINGS, ['employerId'], Table::EMPLOYERS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::HISTORY, ['employerId'], Table::EMPLOYERS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::HMRC_DETAILS, ['employerId'], Table::EMPLOYERS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::LEAVE_SETTINGS, ['employerId'], Table::EMPLOYERS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::PAY_CODES, ['employerId'], Table::EMPLOYERS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::PAY_OPTIONS, ['employerId'], Table::EMPLOYERS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::PAY_RUN, ['employerId'], Table::EMPLOYERS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::PAY_RUN_ENTRIES, ['employerId'], Table::EMPLOYERS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::PAY_RUN_LOG, ['employerId'], Table::EMPLOYERS, ['id'], 'CASCADE', 'CASCADE');
//        $this->addForeignKey(null, Table::PENSION_SCHEME, ['employerId'], Table::EMPLOYERS, ['id'], 'CASCADE', 'CASCADE');
//        $this->addForeignKey(null, Table::PENSION_SELECTION, ['employerId'], Table::EMPLOYERS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::REQUESTS, ['employerId'], Table::EMPLOYERS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::RTI_SUBMISSION_SETTINGS, ['employerId'], Table::EMPLOYERS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::UMBRELLA_SETTINGS, ['employerId'], Table::EMPLOYERS, ['id'], 'CASCADE', 'CASCADE');

        //Country [id]
        $this->addForeignKey(null, Table::ADDRESSES, ['countryId'], Table::COUNTRIES, ['id']);

        //Pay Run [id]
        $this->addForeignKey(null, Table::PAY_RUN_TOTALS, ['payRunId'], Table::PAY_RUN, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::PAY_RUN_ENTRIES, ['payRunId'], Table::PAY_RUN, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::PAY_RUN_IMPORTS, ['payRunId'], Table::PAY_RUN, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::PAY_RUN_LOG, ['payRunId'], Table::PAY_RUN, ['id'], 'CASCADE', 'CASCADE');

        //Pay Run Entries [id]
        $this->addForeignKey(null, Table::CUSTOM_PAY_CODES, ['payRunEntryId'], Table::PAY_RUN_ENTRIES, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::FPS_FIELDS, ['payRunEntryId'], Table::PAY_RUN_ENTRIES, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::NATIONAL_INSURANCE_CALCULATION, ['payRunEntryId'], Table::PAY_RUN_ENTRIES, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::PAY_OPTIONS, ['payRunEntryId'], Table::PAY_RUN_ENTRIES, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::PAY_RUN_TOTALS, ['payRunEntryId'], Table::PAY_RUN_ENTRIES, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::PENSION_SUMMARY, ['payRunEntryId'], Table::PAY_RUN_ENTRIES, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::UMBRELLA_PAYMENT, ['payRunEntryId'], Table::PAY_RUN_ENTRIES, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::VALUE_OVERRIDE, ['payRunEntryId'], Table::PAY_RUN_ENTRIES, ['id'], 'CASCADE', 'CASCADE');

        //Pensions [id]
//        $this->addForeignKey(null, Table::PAY_LINES, ['pensionId'], Table::PENSIONS, ['id'], 'CASCADE', 'CASCADE');
//        $this->addForeignKey(null, Table::PENSION_SCHEME, ['pensionId'], Table::PENSIONS, ['id'], 'CASCADE', 'CASCADE');
//        $this->addForeignKey(null, Table::PENSION_SUMMARY, ['pensionId'], Table::PENSIONS, ['id'], 'CASCADE', 'CASCADE');
//        $this->addForeignKey(null, Table::TEACHER_PENSION_DETAILS, ['pensionId'], Table::PENSIONS, ['id'], 'CASCADE', 'CASCADE');
//        $this->addForeignKey(null, Table::TIER_PENSION_RATE, ['pensionId'], Table::PENSIONS, ['id'], 'CASCADE', 'CASCADE');
//        $this->addForeignKey(null, Table::WORKER_GROUP, ['pensionId'], Table::PENSIONS, ['id'], 'CASCADE', 'CASCADE');

        //Permissions [id]
        $this->addForeignKey(null, Table::PERMISSIONS_USERS, ['permissionId'], Table::PERMISSIONS, ['id'], 'CASCADE', 'CASCADE');

        /** LINKAGE **/
        //Employment Details [id]
        $this->addForeignKey(null, Table::CIS_DETAILS, ['employmentDetailsId'], Table::EMPLOYMENT_DETAILS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::DEPARTMENT, ['employmentDetailsId'], Table::EMPLOYMENT_DETAILS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::DIRECTORSHIP_DETAILS, ['employmentDetailsId'], Table::EMPLOYMENT_DETAILS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::ITEM_RELATIONS, ['employmentDetailsId'], Table::EMPLOYMENT_DETAILS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::LEAVER_DETAILS, ['employmentDetailsId'], Table::EMPLOYMENT_DETAILS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::STARTER_DETAILS, ['employmentDetailsId'], Table::EMPLOYMENT_DETAILS, ['id'], 'CASCADE', 'CASCADE');

        //Auto Enrolment [id]
        $this->addForeignKey(null, Table::AUTO_ENROLMENT_ASSESSMENT, ['autoEnrolmentId'], Table::AUTO_ENROLMENT, ['id'], 'CASCADE', 'CASCADE');

        //Auto Enrolment Assessment [id]
        $this->addForeignKey(null, Table::AUTO_ENROLMENT_ASSESSMENT_ACTION, ['autoEnrolmentAssessmentId'], Table::AUTO_ENROLMENT_ASSESSMENT, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::ITEMS, ['autoEnrolmentAssessmentId'], Table::AUTO_ENROLMENT_ASSESSMENT, ['id'], 'CASCADE', 'CASCADE');

        //Auto Enrolment Settings [id]
//        $this->addForeignKey(null, Table::PENSION_SELECTION, ['autoEnrollmentSettingsId'], Table::AUTO_ENROLMENT_SETTINGS, ['id'], 'CASCADE', 'CASCADE');

        //Cis Subcontractor [id]
        $this->addForeignKey(null, Table::ADDRESSES, ['cisSubcontractorId'], Table::CIS_SUBCONTRACTOR, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::CIS_PARTNERSHIP, ['cisSubcontractorId'], Table::CIS_SUBCONTRACTOR, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::ITEMS, ['cisSubcontractorId'], Table::CIS_SUBCONTRACTOR, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::RTI_EMPLOYEE_NAME, ['cisSubcontractorId'], Table::CIS_SUBCONTRACTOR, ['id'], 'CASCADE', 'CASCADE');

        //Cis Verification Details [id]
        $this->addForeignKey(null, Table::CIS_DETAILS, ['cisVerificationDetailsId'], Table::CIS_VERIFICATION_DETAILS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::CIS_SUBCONTRACTOR, ['cisVerificationDetailsId'], Table::CIS_VERIFICATION_DETAILS, ['id'], 'CASCADE', 'CASCADE');

        //Note [id]
        $this->addForeignKey(null, Table::ITEMS, ['noteId'], Table::NOTE, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::ITEM_RELATIONS, ['noteId'], Table::NOTE, ['id'], 'CASCADE', 'CASCADE');

        //Item [id]
        $this->addForeignKey(null, Table::ITEM_RELATIONS, ['itemId'], Table::ITEMS, ['id'], 'CASCADE', 'CASCADE');

        //Pay Options [id]
        $this->addForeignKey(null, Table::FPS_FIELDS, ['payOptionsId'], Table::PAY_OPTIONS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::PAY_LINES, ['payOptionsId'], Table::PAY_OPTIONS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::TAX_AND_NI, ['payOptionsId'], Table::PAY_OPTIONS, ['id'], 'CASCADE', 'CASCADE');

        //Pension Administrator [id]
        $this->addForeignKey(null, Table::ADDRESSES, ['pensionAdministratorId'], Table::PENSION_ADMINISTRATOR, ['id'], 'CASCADE', 'CASCADE');

        //Pension Provider [id]
//        $this->addForeignKey(null, Table::ADDRESSES, ['pensionProviderId'], Table::PENSION_PROVIDER, ['id'], 'CASCADE', 'CASCADE');

        //Pension Scheme [id]
//        $this->addForeignKey(null, Table::AUTO_ENROLMENT_ASSESSMENT_ACTION, ['pensionSchemeId'], Table::PENSION_SCHEME, ['id'], 'CASCADE', 'CASCADE');
//        $this->addForeignKey(null, Table::BANK_DETAILS, ['pensionSchemeId'], Table::PENSION_SCHEME, ['id'], 'CASCADE', 'CASCADE');
//        $this->addForeignKey(null, Table::CUSTOM_PAY_CODES, ['pensionSchemeId'], Table::PENSION_SCHEME, ['id'], 'CASCADE', 'CASCADE');
//        $this->addForeignKey(null, Table::PENSION_ADMINISTRATOR, ['pensionSchemeId'], Table::PENSION_SCHEME, ['id'], 'CASCADE', 'CASCADE');
//        $this->addForeignKey(null, Table::PENSION_PROVIDER, ['pensionSchemeId'], Table::PENSION_SCHEME, ['id'], 'CASCADE', 'CASCADE');
//        $this->addForeignKey(null, Table::PENSION_SELECTION, ['pensionSchemeId'], Table::PENSION_SCHEME, ['id'], 'CASCADE', 'CASCADE');
//        $this->addForeignKey(null, Table::WORKER_GROUP, ['pensionSchemeId'], Table::PENSION_SCHEME, ['id'], 'CASCADE', 'CASCADE');

        //Pension Summary [id]
//        $this->addForeignKey(null, Table::PENSIONS, ['pensionSummaryId'], Table::PENSION_SUMMARY, ['id'], 'CASCADE', 'CASCADE');
//        $this->addForeignKey(null, Table::PENSION_SCHEME, ['pensionSummaryId'], Table::PENSION_SUMMARY, ['id'], 'CASCADE', 'CASCADE');

        //Rti Agent [id]
        $this->addForeignKey(null, Table::ADDRESSES, ['rtiAgentId'], Table::RTI_AGENT, ['id'], 'CASCADE', 'CASCADE');

        //Rti Employee Address [id]
        $this->addForeignKey(null, Table::ADDRESSES, ['rtiEmployeeAddressId'], Table::RTI_EMPLOYEE_ADDRESS, ['id'], 'CASCADE', 'CASCADE');

        //Rti Submission Settings [id]
        $this->addForeignKey(null, Table::RTI_AGENT, ['rtiSubmissionSettingsId'], Table::RTI_SUBMISSION_SETTINGS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::RTI_CONTACT, ['rtiSubmissionSettingsId'], Table::RTI_SUBMISSION_SETTINGS, ['id'], 'CASCADE', 'CASCADE');

        //Starter Details [id]
        $this->addForeignKey(null, Table::OVERSEAS_EMPLOYER_DETAILS, ['starterDetailsId'], Table::STARTER_DETAILS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::PENSIONER_PAYROLL, ['starterDetailsId'], Table::STARTER_DETAILS, ['id'], 'CASCADE', 'CASCADE');

        //Worker Group [id]
        $this->addForeignKey(null, Table::PENSION_SUMMARY, ['workerGroupId'], Table::WORKER_GROUP, ['id']);

        /** CRAFT **/
        // Elements [id]
        $this->addForeignKey(null, Table::BENEFIT_PROVIDERS, ['id'], CraftTable::ELEMENTS, ['id'], 'CASCADE', 'CASCADE' );
        $this->addForeignKey(null, Table::BENETFIT_TYPE_DENTAL, ['id'], CraftTable::ELEMENTS, ['id'], 'CASCADE', 'CASCADE' );
        $this->addForeignKey(null, Table::BENETFIT_TYPE_GROUP_CRITICAL_ILLNESS_COVER, ['id'], CraftTable::ELEMENTS, ['id'], 'CASCADE', 'CASCADE' );
        $this->addForeignKey(null, Table::BENETFIT_TYPE_GROUP_DEATH_IN_SERVICE, ['id'], CraftTable::ELEMENTS, ['id'], 'CASCADE', 'CASCADE' );
        $this->addForeignKey(null, Table::BENETFIT_TYPE_GROUP_INCOME_PROTECTION, ['id'], CraftTable::ELEMENTS, ['id'], 'CASCADE', 'CASCADE' );
        $this->addForeignKey(null, Table::BENETFIT_TYPE_GROUP_LIFE_ASSURANCE, ['id'], CraftTable::ELEMENTS, ['id'], 'CASCADE', 'CASCADE' );
        $this->addForeignKey(null, Table::BENETFIT_TYPE_HEALTH_CASH_PLAN, ['id'], CraftTable::ELEMENTS, ['id'], 'CASCADE', 'CASCADE' );
        $this->addForeignKey(null, Table::BENETFIT_TYPE_PRIVATE_MEDICAL_INSURANCE, ['id'], CraftTable::ELEMENTS, ['id'], 'CASCADE', 'CASCADE' );
        $this->addForeignKey(null, Table::EMPLOYEES, ['id'], CraftTable::ELEMENTS, ['id'], 'CASCADE', 'CASCADE' );
        $this->addForeignKey(null, Table::EMPLOYERS, ['id'], CraftTable::ELEMENTS, ['id'], 'CASCADE', 'CASCADE' );
        $this->addForeignKey(null, Table::PAY_RUN, ['id'], CraftTable::ELEMENTS, ['id'], 'CASCADE', 'CASCADE' );
        $this->addForeignKey(null, Table::PAY_RUN_ENTRIES, ['id'], CraftTable::ELEMENTS, ['id'], 'CASCADE', 'CASCADE' );
        $this->addForeignKey(null, Table::REQUESTS, ['id'], CraftTable::ELEMENTS, ['id'], 'CASCADE', 'CASCADE' );

        // User [id]
        $this->addForeignKey(null, Table::EMPLOYEES, ['userId'], CraftTable::USERS, ['id']);
        $this->addForeignKey(null, Table::HISTORY, ['administerId'], CraftTable::USERS, ['id']);
        $this->addForeignKey(null, Table::PAY_RUN_IMPORTS, ['approvedBy'], CraftTable::USERS, ['id']);
        $this->addForeignKey(null, Table::PAY_RUN_IMPORTS, ['uploadedBy'], CraftTable::USERS, ['id']);
        $this->addForeignKey(null, Table::PERMISSIONS_USERS, ['userId'], CraftTable::USERS, ['id']);
        $this->addForeignKey(null, Table::REQUESTS, ['administerId'], CraftTable::USERS, ['id']);
    }

    /**
     * Insert the default data.
     */
    public function insertDefaultData(): void
    {
        $this->_createPermissions();
        $this->_defaultCountries();
    }

    /**
     * Insert default countries data.
     */
    private function _defaultCountries(): void
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
     * Create the permissions for the Company Users
     */
    private function _createPermissions(): void
    {
        $rows = [];

        $rows[] = ['access:employers'];
        $rows[] = ['access:employer'];
        $rows[] = ['access:groupbenefits'];
        $rows[] = ['access:voluntarybenefits'];
        $rows[] = ['access:history'];
        $rows[] = ['access:requests'];
        $rows[] = ['manage:notifications'];
        $rows[] = ['manage:employees'];
        $rows[] = ['manage:employer'];
        $rows[] = ['manage:benefits'];
        $rows[] = ['manage:requests'];
        $rows[] = ['purchase:groupbenefits'];
        $rows[] = ['purchase:voluntarybenefits'];

        $this->batchInsert(Table::PERMISSIONS, ['name'], $rows);
    }
}
