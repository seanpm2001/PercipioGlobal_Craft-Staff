<?php

namespace percipiolondon\staff\migrations;

use Craft;
use craft\db\Migration;
use percipiolondon\staff\db\Table;

/**
 * m220518_085726_unique_index_fix migration.
 */
class m220518_113426_unique_index_fix extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $table = Craft::$app->db->schema->getTableSchema(Table::PAY_RUN_TOTALS);
        $uniqueIndexes = Craft::$app->db->schema->findIndexes(Table::PAY_RUN_TOTALS);
        $foreignKeys = $table->foreignKeys;

        foreach($foreignKeys as $fk => $foreignKey) {
            if($foreignKey['payRunEntryId'] ?? '' === 'id') {

                $this->dropForeignKey($fk, Table::PAY_RUN_TOTALS);

                foreach($uniqueIndexes as $idx => $index){
                    if($index['columns'][0] === 'payRunEntryId') {
                        $this->dropIndex($idx, Table::PAY_RUN_TOTALS);
                    }
                }
            }
        }

        $this->createIndex(null, Table::PAY_RUN_TOTALS, 'payRunEntryId', false);
        $this->addForeignKey(null, Table::PAY_RUN_TOTALS, ['payRunEntryId'], Table::PAY_RUN_ENTRIES, ['id'], 'CASCADE', 'CASCADE');

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m220518_113426_unique_index_fix cannot be reverted.\n";
        return false;
    }
}
