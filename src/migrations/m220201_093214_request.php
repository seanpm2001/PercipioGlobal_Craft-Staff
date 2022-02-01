<?php

namespace percipiolondon\craftstaff\migrations;

use Craft;
use craft\db\Migration;
use craft\helpers\MigrationHelper;
use percipiolondon\craftstaff\db\Table;
use yii\base\NotSupportedException;

/**
 * m220201_093214_request migration.
 */
class m220201_093214_request extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        //request table
        $tableSchema = Craft::$app->db->schema->getTableSchema(Table::STAFF_REQUESTS);
        if ($tableSchema === null) {
            $this->createTable(
                Table::STAFF_REQUESTS,
                [
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
                ]
            );

            $this->createIndexes();
            $this->addForeignKeys();
            // Refresh the db schema caches
            Craft::$app->db->schema->refresh();
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m220201_093214_request cannot be reverted.\n";
        return false;
    }

    /**
     * Creates the indexes needed for the Records used by the plugin
     *
     * @return void
     */
    protected function createIndexes()
    {
        $this->createIndex(null, Table::STAFF_REQUESTS, 'element', false);
    }

    /**
     * Creates the foreign keys needed for the Records used by the plugin
     *
     * @return void
     */
    protected function addForeignKeys()
    {
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
    }
}
