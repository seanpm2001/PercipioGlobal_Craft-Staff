<?php

namespace percipiolondon\staff\records;

use craft\db\ActiveRecord;
use percipiolondon\staff\db\Table;

/**
 * @property string $contact;
 * @property string $agent;
 *
 * @property string $senderType;
 * @property string $senderId;
 * @property string $password;
 * @property boolean $excludeNilPaid;
 * @property boolean $includeHashCrossRef;
 * @property boolean $autoSubmitFps;
 * @property boolean $testInLive;
 * @property boolean $useTestGateway;
 * @property string $overrideTimestampValue;
 */

class RtiSubmissionSettings extends ActiveRecord
{
    // Public Static Methods
    // =========================================================================

    /**
     * Declares the name of the database table associated with this AR class.
     * By default this method returns the class name as the table name by calling [[Inflector::camel2id()]]
     * with prefix [[Connection::tablePrefix]]. For example if [[Connection::tablePrefix]] is `tbl_`,
     * `Customer` becomes `tbl_customer`, and `OrderItem` becomes `tbl_order_item`. You may override this method
     * if the table is not named after this convention.
     *
     * By convention, tables created by plugins should be prefixed with the plugin
     * name and an underscore.
     *
     * @return string the table name
     */
    public static function tableName(): string
    {
        return Table::BANK_DETAILS;
    }
}
