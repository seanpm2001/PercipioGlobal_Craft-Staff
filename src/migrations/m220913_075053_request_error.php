<?php

namespace percipiolondon\staff\migrations;

use Craft;
use craft\db\Migration;
use percipiolondon\staff\db\Table;

/**
 * m220913_075053_request_error migration.
 */
class m220913_075053_request_error extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        // Place migration code here...
        $this->addColumn(Table::REQUESTS, 'error', $this->string(255)->notNull());
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m220913_075053_request_error cannot be reverted.\n";
        return false;
    }
}
