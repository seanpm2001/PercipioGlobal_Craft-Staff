<?php

namespace percipiolondon\staff\migrations;

use Craft;
use craft\db\Migration;
use percipiolondon\staff\db\Table;

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

        $this->createTable(Table::SETTINGS, [
            'id' => $this->primaryKey(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
            //fields
            'name' => $this->string(255)->notNull(),
        ]);

        $this->createIndex(null, Table::SETTINGS, 'name', true);

        // create settings from employees
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

        $rows[] = ['notifications:app'];
        $rows[] = ['notifications:benefits'];
        $rows[] = ['notifications:employees'];
        $rows[] = ['notifications:payroll'];
        $rows[] = ['notifications:pension'];
        $rows[] = ['notifications:system'];

        $this->batchInsert(Table::SETTINGS, ['name'], $rows);
    }
}
