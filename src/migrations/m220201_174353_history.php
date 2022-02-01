<?php

namespace percipiolondon\craftstaff\migrations;

use Craft;
use craft\db\Migration;
use craft\helpers\MigrationHelper;
use percipiolondon\craftstaff\db\Table;
use yii\base\NotSupportedException;

/**
 * m220201_174353_history migration.
 */
class m220201_174353_history extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        //history table
        $tableSchema = Craft::$app->db->schema->getTableSchema(Table::STAFF_HISTORY);
        if ($tableSchema === null) {
            $this->createTable(
                Table::STAFF_HISTORY,
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                    'employerId' => $this->integer()->notNull(),
                    'employeeId' => $this->integer()->notNull(),
                    'administerId' => $this->integer()->notNull(),
                    'message' => $this->string(255)->notNull(),
                    'type' => $this->string()->notNull(),
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
        echo "m220201_174353_history cannot be reverted.\n";
        return false;
    }

    /**
     * Creates the indexes needed for the Records used by the plugin
     *
     * @return void
     */
    protected function createIndexes()
    {
        $this->createIndex(null, Table::STAFF_HISTORY, 'type', false);
    }

    /**
     * Creates the foreign keys needed for the Records used by the plugin
     *
     * @return void
     */
    protected function addForeignKeys()
    {
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
}
