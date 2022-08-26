<?php

namespace percipiolondon\staff\migrations;

use Craft;
use craft\db\Migration;
use craft\db\Table as CraftTable;
use percipiolondon\staff\db\Table;
use percipiolondon\staff\elements\Employee;
use percipiolondon\staff\elements\SettingsEmployee;
use percipiolondon\staff\jobs\CreateSettingsJob;
use percipiolondon\staff\records\Settings;

/**
 * m220824_111259_settings migration.
 */
class m220824_111259_settings extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        // create settings
        $tableSchema = Craft::$app->db->schema->getTableSchema(Table::SETTINGS);
        if ($tableSchema) {
            $this->dropTable(Table::SETTINGS);
        }

        // main settings
        $this->createTable(Table::SETTINGS, [
            'id' => $this->primaryKey(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
            //fields
            'name' => $this->string(255)->notNull(),
        ]);

        $this->createIndex(null, Table::SETTINGS, 'name', true);

        // employee settings
        $tableSchema = Craft::$app->db->schema->getTableSchema(Table::SETTINGS_EMPOYEE);
        if ($tableSchema) {
            $this->dropTable(Table::SETTINGS_EMPOYEE);
        }

        $this->createTable(Table::SETTINGS_EMPOYEE, [
            'id' => $this->primaryKey(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
            //FK
            //internal
            'settingsId' => $this->integer()->notNull(), //create FK to Settings [id]
            'employeeId' => $this->integer()->notNull(), //create FK to Employee [id]
        ]);

        $this->createIndex(null, Table::SETTINGS_EMPOYEE, 'settingsId', false);
        $this->createIndex(null, Table::SETTINGS_EMPOYEE, 'employeeId', false);

        $this->addForeignKey(null, Table::SETTINGS_EMPOYEE, ['settingsId'], Table::SETTINGS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::SETTINGS_EMPOYEE, ['employeeId'], Table::EMPLOYEES, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::SETTINGS_EMPOYEE, ['id'], CraftTable::ELEMENTS, ['id'], 'CASCADE', 'CASCADE' );

        // admin settings
        $tableSchema = Craft::$app->db->schema->getTableSchema(Table::SETTINGS_ADMIN);
        if ($tableSchema) {
            $this->dropTable(Table::SETTINGS_ADMIN);
        }

        $this->createTable(Table::SETTINGS_ADMIN, [
            'id' => $this->primaryKey(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
            //FK
            //internal
            'settingsId' => $this->integer()->notNull(), //create FK to Settings [id]
            'userId' => $this->integer()->notNull(), //create FK to Employee [id]
        ]);

        $this->createIndex(null, Table::SETTINGS_ADMIN, 'settingsId', false);
        $this->createIndex(null, Table::SETTINGS_ADMIN, 'userId', false);

        $this->addForeignKey(null, Table::SETTINGS_ADMIN, ['settingsId'], Table::SETTINGS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::SETTINGS_ADMIN, ['id'], CraftTable::ELEMENTS, ['id'], 'CASCADE', 'CASCADE' );
        $this->addForeignKey(null, Table::SETTINGS_ADMIN, ['userId'], CraftTable::USERS, ['id']);

        $this->_createSettings();
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m220824_111259_settings cannot be reverted.\n";
        return false;
    }

    private function _createSettings(): void
    {
        $rows = [];

        $rows[] = ['app:privacy-toggle'];
        $rows[] = ['notifications:app'];
        $rows[] = ['notifications:benefit'];
        $rows[] = ['notifications:employee'];
        $rows[] = ['notifications:payroll'];
        $rows[] = ['notifications:pension'];
        $rows[] = ['notifications:system'];

        $this->batchInsert(Table::SETTINGS, ['name'], $rows);

        $settings = Settings::find()->all();
        $employees = Employee::find()->all();

        foreach ($employees as $employee) {
            foreach ($settings as $setting) {
                $queue = Craft::$app->getQueue();
                $queue->push(new CreateSettingsJob([
                    'description' => 'Set all settings for employee '.$employee->id,
                    'criteria' => [
                        'employeeId' => $employee->id,
                        'settingsId' => $setting->id
                    ],
                ]));
            }
        }
    }
}
