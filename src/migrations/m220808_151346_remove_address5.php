<?php

namespace percipiolondon\staff\migrations;

use Craft;
use craft\db\Migration;
use percipiolondon\staff\db\Table;

/**
 * m220808_151346_remove_address5 migration.
 */
class m220808_151346_remove_address5 extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->dropColumn(Table::ADDRESSES, 'address5');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m220808_151346_remove_address5 cannot be reverted.\n";
        return false;
    }
}
