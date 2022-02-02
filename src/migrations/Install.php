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
        $this->createTable(Table::STAFF_EMPLOYEES, [
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
            // @TODO: create own table
            'personalDetails' => $this->longText(),
            // @TODO: create own table
            'employmentDetails' => $this->longText(),
            // @TODO: create own table
            'autoEnrolment' => $this->longText(),
            // @TODO: create own table
            'leaveSettings' => $this->longText(),
            // @TODO: create own table
            'rightToWork' => $this->longText(),
            // @TODO: create own table
            'bankDetails' => $this->longText(),
            'status' => $this->string(255)->notNull()->defaultValue('Current'),
            'aeNotEnroledWarning' => $this->boolean()->defaultValue(0),
            'niNumber' => $this->string(255),
            'sourceSystemId' => $this->string(255),
        ]);

        $this->createTable(Table::STAFF_EMPLOYERS, [
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
            // @TODO: create own table
            'address' => $this->longText(),
            // @TODO: create own table
            'hmrcDetails' => $this->longText(),
            'startYear' => $this->string(255)->notNull(),
            'currentYear' => $this->string(255)->notNull(),
            'employeeCount' => $this->integer()->notNull()->defaultValue(0),
            // @TODO: create own table
            'defaultPayOptions' => $this->longText(),
        ]);

        $this->createTable(Table::STAFF_HISTORY,
            [
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
            ]
        );

        $this->createTable(Table::STAFF_PAYRUN, [
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

        $this->createTable(Table::STAFF_PAYRUN_LOG, [
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

        $this->createTable(Table::STAFF_PAYRUNENTRIES, [
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
            'holidayAccured' => $this->double()->defaultValue(0),
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

        $this->createTable(Table::STAFF_PERMISSIONS, [
            'id' => $this->primaryKey(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
            'name' => $this->string(255)->notNull()->defaultValue(''),
        ]);

        $this->createTable(Table::STAFF_PERMISSIONS_USERS, [
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
        //      "Address": {
        //        "Line1": "12 High Street",
        //        "Line2": "Belsize Park",
        //        "Line3": "London",
        //        "Line4": null,
        //        "Line5": null,
        //        "PostCode": "NW3 4BU",
        //        "Country": "England"
        //      },
        //      "PartnerDetails": null
        //    },
        $this->createTable(Table::STAFF_PERSONAL_DETAILS,
            [
                'id' => $this->primaryKey(),
                'employeeId' => $this->integer()->notNull(),
                'maritalStatus' => $this->string(255)->notNull(),
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
                'gender' => $this->string(255)->notNull(),
                'niNumber' => $this->string(255)->notNull(),
                'passportNumber' => $this->string(255)->notNull(),
            ]
        );

        $this->createTable(Table::STAFF_REQUESTS, [
            'id' => $this->primaryKey(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'dateAdministered' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
            'employerId' => $this->integer()->notNull(),
            'employeeId' => $this->integer()->notNull(),
            'administerId' => $this->integer()->notNull(),
            'data' => $this->longText(),
            'section' => $this->string()->notNull(),
            'element' => $this->string()->notNull(),
            'status' => $this->string()->notNull(),
            'note' => $this->string(255),
        ]);

        $this->createTable(Table::STAFF_USERS, [
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
        $this->dropTableIfExists(Table::STAFF_EMPLOYEES);
        $this->dropTableIfExists(Table::STAFF_EMPLOYERS);
        $this->dropTableIfExists(Table::STAFF_HISTORY);
        $this->dropTableIfExists(Table::STAFF_PAYRUN);
        $this->dropTableIfExists(Table::STAFF_PAYRUN_LOG);
        $this->dropTableIfExists(Table::STAFF_PAYRUNENTRIES);
        $this->dropTableIfExists(Table::STAFF_PERMISSIONS);
        $this->dropTableIfExists(Table::STAFF_PERMISSIONS_USERS);
        $this->dropTableIfExists(Table::STAFF_PERSONAL_DETAILS);
        $this->dropTableIfExists(Table::STAFF_REQUESTS);
        $this->dropTableIfExists(Table::STAFF_USERS);

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
            Table::STAFF_EMPLOYEES,
            Table::STAFF_EMPLOYERS,
            Table::STAFF_HISTORY,
            Table::STAFF_PAYRUN,
            Table::STAFF_PAYRUN_LOG,
            Table::STAFF_PAYRUNENTRIES,
            Table::STAFF_PERMISSIONS,
            Table::STAFF_PERMISSIONS_USERS,
            Table::STAFF_PERSONAL_DETAILS,
            Table::STAFF_REQUESTS,
            Table::STAFF_USERS
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
        //$this->_defaultCountries();
        //$this->_defaultCounties();
    }


    // Protected Methods
    // =========================================================================



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
     * Removes the tables needed for the Records used by the plugin
     *
     * @return void
     */
    protected function removeTables()
    {
        // staff_employee table
        $this->dropTableIfExists(Table::STAFF_EMPLOYEES);

        // staff_payrunentries table
        $this->dropTableIfExists(Table::STAFF_PAYRUNENTRIES);

        // staff_user table
        $this->dropTableIfExists(Table::STAFF_USERS);

        // staff_payrun table
        $this->dropTableIfExists(Table::STAFF_PAYRUN);

        // staff_payrun_log table
        $this->dropTableIfExists(Table::STAFF_PAYRUN_LOG);

        // staff_employer table
        $this->dropTableIfExists(Table::STAFF_EMPLOYERS);

        // staff_permissions_users table
        $this->dropTableIfExists(Table::STAFF_PERMISSIONS_USERS);

        // staff_permissions table
        $this->dropTableIfExists(Table::STAFF_PERMISSIONS);

        // staff_requests table
        $this->dropTableIfExists(Table::STAFF_REQUESTS);

        // staff_history table
        $this->dropTableIfExists(Table::STAFF_HISTORY);
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
