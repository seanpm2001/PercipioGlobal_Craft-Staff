<?php
/**
 * staff-management plugin for Craft CMS 3.x
 *
 * Craft Staff Management provides an HR solution for payroll and benefits
 *
 * @link      http://percipio.london
 * @copyright Copyright (c) 2021 Percipio
 */

namespace percipiolondon\craftstaff\migrations;

use craft\helpers\MigrationHelper;
use percipiolondon\craftstaff\db\Table;

use Craft;
use craft\config\DbConfig;
use craft\db\Migration;
use yii\base\NotSupportedException;

/**
 * staff-management Install Migration
 *
 * If your plugin needs to create any custom database tables when it gets installed,
 * create a migrations/ folder within your plugin folder, and save an Install.php file
 * within it using the following template:
 *
 * If you need to perform any additional actions on install/uninstall, override the
 * safeUp() and safeDown() methods.
 *
 * @author    Percipio
 * @package   staff
 * @since     1.0.0-alpha.1
 */
class Install extends Migration
{
    // Public Properties
    // =========================================================================

    /**
     * @var string The database driver to use
     */
    public $driver;

    // Public Methods
    // =========================================================================

    /**
     * This method contains the logic to be executed when applying this migration.
     * This method differs from [[up()]] in that the DB logic implemented here will
     * be enclosed within a DB transaction.
     * Child classes may implement this method instead of [[up()]] if the DB logic
     * needs to be within a transaction.
     *
     * @return boolean return a false value to indicate the migration fails
     * and should not proceed further. All other return values mean the migration succeeds.
     */
    public function safeUp()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        if ($this->createTables()) {
            $this->createIndexes();
            $this->addForeignKeys();
            // Refresh the db schema caches
            Craft::$app->db->schema->refresh();
            $this->insertDefaultData();
        }

        return true;
    }

    /**
     * This method contains the logic to be executed when removing this migration.
     * This method differs from [[down()]] in that the DB logic implemented here will
     * be enclosed within a DB transaction.
     * Child classes may implement this method instead of [[down()]] if the DB logic
     * needs to be within a transaction.
     *
     * @return boolean return a false value to indicate the migration fails
     * and should not proceed further. All other return values mean the migration succeeds.
     */
    public function safeDown()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        $this->dropForeignKeys();
        $this->removeTables();

        return true;
    }

    // Protected Methods
    // =========================================================================

    /**
     * Creates the tables needed for the Records used by the plugin
     *
     * @return bool
     */
    protected function createTables()
    {
        $tablesCreated = false;

    // staff_employer table
        $tableSchema = Craft::$app->db->schema->getTableSchema(Table::STAFF_EMPLOYERS);
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                Table::STAFF_EMPLOYERS,
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                    'slug' => $this->string(255)->notNull(),
                    'siteId' => $this->integer()->notNull(),
                    // Custom columns in the table
                    'staffologyId' => $this->string(255)->notNull(),
                    'name' => $this->string(255)->notNull(),
//                   'logoId' =>  $this->integer()->notNull(),
                    'crn' => $this->string(),
                    'address' => $this->longText(),
                    'hmrcDetails' => $this->longText(),
                    'startYear' => $this->string(255)->notNull(),
                    'currentYear' => $this->string(255)->notNull(),
                    'employeeCount' => $this->integer()->notNull()->defaultValue(0),
                    'defaultPayOptions' => $this->longText(),
                ]
            );
        }

    // staff_employee table
        $tableSchema = Craft::$app->db->schema->getTableSchema(Table::STAFF_EMPLOYEES);
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                Table::STAFF_EMPLOYEES,
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                    'siteId' => $this->integer()->notNull(),
                // Custom columns in the table
                    'staffologyId' => $this->string(255)->notNull(),
                    'employerId' => $this->integer()->notNull(),
                    'userId' => $this->integer(),
                    'isDirector' => $this->boolean(),
                    'personalDetails' => $this->longText(),
                    'employmentDetails' => $this->longText(),
                    'autoEnrolment' => $this->longText(),
                    'leaveSettings' => $this->longText(),
                    'rightToWork' => $this->longText(),
                    'bankDetails' => $this->longText(),
                    'status' => $this->string(255)->notNull()->defaultValue('Current'),
                    'aeNotEnroledWarning' => $this->boolean()->defaultValue(0),
                    'niNumber' => $this->string(255),
                    'sourceSystemId' => $this->string(255),
                ]
            );
        }

    // staff_payrun_log table
        $tableSchema = Craft::$app->db->schema->getTableSchema(Table::STAFF_PAYRUN_LOG);
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                Table::STAFF_PAYRUN_LOG,
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                    'siteId' => $this->integer()->notNull(),
                // Custom columns in the table
                    'employeeCount' => $this->integer()->notNull()->defaultValue(0),
                    'taxYear' => $this->string(255)->notNull()->defaultValue(''),
                    'lastPeriodNumber' => $this->integer()->notNull()->defaultValue(0),
                    'url' => $this->string(255)->notNull()->defaultValue(0),
                    'employerId' => $this->integer()->notNull()->defaultValue(0),
                    'payRunId' => $this->integer()->notNull()->defaultValue(0),
                ]
            );
        }

    // staff_payrun table
        $tableSchema = Craft::$app->db->schema->getTableSchema(Table::STAFF_PAYRUN);
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                Table::STAFF_PAYRUN,
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                    'siteId' => $this->integer()->notNull(),
                    // Custom columns in the table
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
                    'totals' => $this->longText()->notNull(),
                    'state' => $this->string(255)->notNull()->defaultValue(''),
                    'isClosed' => $this->boolean()->notNull(),
                    'dateClosed' => $this->dateTime(),
                    'url' => $this->string()->defaultValue(''),
                    'employerId' => $this->integer()->notNull()->defaultValue(null),
                ]
            );
        }

    // STAFF_PAYRUNENTRIES table
        $tableSchema = Craft::$app->db->schema->getTableSchema(Table::STAFF_PAYRUNENTRIES);
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                Table::STAFF_PAYRUNENTRIES,
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                    'siteId' => $this->integer()->notNull(),
                // Custom columns in the table
                    'staffologyId' => $this->string(255)->notNull(),
                    'payRunId' => $this->integer()->notNull()->defaultValue(0),
                    'taxYear' => $this->string(255)->defaultValue(''),
                    'startDate' => $this->dateTime(),
                    'endDate' => $this->dateTime(),
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
                    'payOptions' => $this->longText(),
                    'pensionSummary' => $this->longText(),
                    'totals' => $this->longText(),
                    'periodOverrides' => $this->longText(),
                    'totalsYtd' => $this->longText(),
                    'totalsYtdOverrides' => $this->longText(),
                    'forcedCisVatAmount' => $this->double()->defaultValue(0),
                    'holidayAccured' => $this->double()->defaultValue(0),
                    'state' => $this->string(255)->defaultValue('Open'),
                    'isClosed' => $this->boolean(),
                    'manualNi' => $this->boolean(),
                    'nationalInsuranceCalculation' => $this->longText(),
                    'payrollCodeChanged' => $this->boolean(),
                    'aeNotEnroledWarning' => $this->boolean(),
                    'fps' => $this->longText(),
                    'receivingOffsetPay' => $this->boolean(),
                    'paymentAfterLearning' => $this->boolean(),
                    'umbrellaPayment' => $this->longText(),
                    'employee' => $this->longText(),
                    'pdf' => $this->string()->defaultValue(''),
                    'employerId' => $this->integer()->notNull()->defaultValue(null),
                    'employeeId' => $this->integer()->notNull()->defaultValue(null),
                ]
            );
        }

    // staff_hardinguser table
        $tableSchema = Craft::$app->db->schema->getTableSchema(Table::STAFF_USERS);
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                Table::STAFF_USERS,
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                    'siteId' => $this->integer()->notNull(),
                // Custom columns in the table
                    'staffologyId' => $this->string(255)->notNull(),
                    'metadata' => $this->longText()->notNull(),
                ]
            );
        }

    // staff_permissions table
        $tableSchema = Craft::$app->db->schema->getTableSchema(Table::STAFF_PERMISSIONS);
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                Table::STAFF_PERMISSIONS,
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                    // Custom columns in the table
                    'name' => $this->string(255)->notNull()->defaultValue(''),
                ]
            );
        }

        // staff_permissions table
        $tableSchema = Craft::$app->db->schema->getTableSchema(Table::STAFF_PERMISSIONS_USERS);
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                Table::STAFF_PERMISSIONS_USERS,
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                    // Custom columns in the table
                    'permissionId' => $this->integer()->notNull()->defaultValue(0),
                    'userId' => $this->integer()->defaultValue(null),
                    'employeeId' => $this->integer()->notNull()->defaultValue(0),
                ]
            );
        }

        return $tablesCreated;
    }

    /**
     * Creates the indexes needed for the Records used by the plugin
     *
     * @return void
     */
    protected function createIndexes()
    {
        $this->createIndex(null, Table::STAFF_EMPLOYERS, 'name', false);
        $this->createIndex(null, Table::STAFF_EMPLOYEES, 'niNumber', false);
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

//        $this->addForeignKey(
//            $this->db->getForeignKeyName(Table::STAFF_EMPLOYERS, 'logoId'),
//            Table::STAFF_EMPLOYERS,
//            'logoId',
//            \craft\db\Table::ASSETS,
//            'id',
//            'CASCADE',
//            'CASCADE'
//        );

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

    // staff_permissions_users table
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
    }

    /**
     * Drop the foreign keys
     */
    public function dropForeignKeys()
    {
        $tables = [
            Table::STAFF_EMPLOYERS,
            Table::STAFF_PAYRUN,
            Table::STAFF_PAYRUNENTRIES,
            Table::STAFF_PAYRUN_LOG,
            Table::STAFF_PERMISSIONS_USERS,
            Table::STAFF_PERMISSIONS,
            Table::STAFF_EMPLOYERS,
            Table::STAFF_USERS,
        ];
        foreach ($tables as $table) {
            $this->_dropForeignKeyToAndFromTable($table);
        }
    }

    /**
     * Populates the DB with the default data.
     *
     * @return void
     */
    protected function insertDefaultData()
    {
        $this->_createPermissions();
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
