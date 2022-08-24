<?php

namespace percipiolondon\staff\migrations;

use Craft;
use craft\db\Migration;
use craft\db\Table as CraftTable;
use percipiolondon\staff\db\Table;

/**
 * m220824_095537_notifications migration.
 */
class m220824_095537_notifications extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $tableSchema = Craft::$app->db->schema->getTableSchema(Table::NOTIFICATIONS);
        if ($tableSchema) {
            $this->dropTable(Table::NOTIFICATIONS);
        }

        $this->createTable(Table::NOTIFICATIONS, [
            'id' => $this->primaryKey(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
            //FK
            //internal
            'employerId' => $this->integer()->notNull(), //create FK to Employer [id]
            'employeeId' => $this->integer()->notNull(), //create FK to Employee [id]
            //fields
            'message' => $this->string(255)->notNull(),
            'type' => $this->enum('type', ['app', 'system', 'payroll', 'pension', 'employee', 'benefit']),
            'viewed' => $this->boolean()
        ]);

        $this->createIndex(null, Table::NOTIFICATIONS, 'employeeId', false);
        $this->createIndex(null, Table::NOTIFICATIONS, 'employerId', false);
        $this->createIndex(null, Table::NOTIFICATIONS, 'type', false);

        $this->addForeignKey(null, Table::NOTIFICATIONS, ['employeeId'], Table::EMPLOYEES, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::NOTIFICATIONS, ['employerId'], Table::EMPLOYERS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::NOTIFICATIONS, ['id'], CraftTable::ELEMENTS, ['id'], 'CASCADE', 'CASCADE' );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m220824_095537_notifications cannot be reverted.\n";
        return false;
    }
}
