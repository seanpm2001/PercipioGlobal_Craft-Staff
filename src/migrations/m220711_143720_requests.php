<?php

namespace percipiolondon\staff\migrations;

use Craft;
use craft\db\Migration;
use craft\db\Table as CraftTable;
use percipiolondon\staff\db\Table;

/**
 * m220711_143720_requests migration.
 */
class m220711_143720_requests extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->dropTable(Table::REQUESTS);

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
            'data' => $this->longText()->notNull(),
            'type' => $this->string()->notNull(),
            'status' => $this->enum('contributionLevelType', ['pending', 'approved', 'declined', 'canceled']),
            'note' => $this->mediumText(),
        ]);

        $this->createIndex(null, Table::REQUESTS, 'administerId', false);
        $this->createIndex(null, Table::REQUESTS, 'status', false);

        $this->addForeignKey(null, Table::REQUESTS, ['employerId'], Table::EMPLOYERS, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::REQUESTS, ['employeeId'], Table::EMPLOYEES, ['id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey(null, Table::REQUESTS, ['administerId'], CraftTable::USERS, ['id']);
        $this->addForeignKey(null, Table::REQUESTS, ['id'], CraftTable::ELEMENTS, ['id'], 'CASCADE', 'CASCADE' );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m220711_143720_requests cannot be reverted.\n";
        return false;
    }
}
