<?php

namespace percipiolondon\staff\migrations;

use Craft;
use craft\db\Migration;
use craft\db\Table as CraftTable;
use percipiolondon\staff\db\Table;

/**
 * m220819_135101_history migration.
 */
class m220819_135101_history extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->dropTable(Table::HISTORY);

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
            'type' => $this->enum('type', ['system', 'payroll', 'pension', 'employee', 'benefit']),
        ]);

        $this->createIndex(null, Table::HISTORY, 'employeeId', false);
        $this->createIndex(null, Table::HISTORY, 'employerId', false);
        $this->createIndex(null, Table::HISTORY, 'administerId', false);

        $this->addForeignKey(null, Table::HISTORY, ['employeeId'], Table::EMPLOYEES, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::HISTORY, ['employerId'], Table::EMPLOYERS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::HISTORY, ['administerId'], CraftTable::USERS, ['id']);
        $this->addForeignKey(null, Table::HISTORY, ['id'], CraftTable::ELEMENTS, ['id'], 'CASCADE', 'CASCADE' );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m220819_135101_history cannot be reverted.\n";
        return false;
    }
}
