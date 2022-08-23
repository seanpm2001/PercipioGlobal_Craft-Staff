<?php

namespace percipiolondon\staff\migrations;

use Craft;
use craft\db\Migration;
use craft\db\Table as CraftTable;
use percipiolondon\staff\db\Table;

/**
 * m220621_133421_benefit_providers migration.
 */
class m220621_133421_benefit_providers extends Migration
{

    /**
     * @var string
     */
    public string|null $driver;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;

        if ($this->createTables()) {
            $this->createIndexes();
            $this->createForeignKeys();
            Craft::$app->db->schema->refresh();
        }
    }

    /**
     * Creates the tables for Staff Management
     */
    public function createTables(): bool {
        $tableCreated = false;

        $tableSchema = Craft::$app->db->schema->getTableSchema(Table::BENEFIT_PROVIDERS);
        if ($tableSchema === null) {
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

            $tableCreated = true;
        }

        return $tableCreated;
    }

    /**
     * Creates the indexes
     */
    public function createIndexes(): void
    {
        $this->createIndex(null, Table::BENEFIT_PROVIDERS, 'name', true);
    }

    public function createForeignKeys(): void
    {
        $this->addForeignKey(null, Table::BENEFIT_PROVIDERS, ['id'], CraftTable::ELEMENTS, ['id'], 'CASCADE', 'CASCADE' );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m220621_133421_benefit_providers cannot be reverted.\n";
        return false;
    }
}
