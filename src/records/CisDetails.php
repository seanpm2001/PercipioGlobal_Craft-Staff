<?php

namespace percipiolondon\staff\records;

use craft\db\ActiveRecord;
use percipiolondon\staff\db\Table;

/**
 * @property int cisVerificationDetailsId;
 * @property int employmentDetailsId;
 *
 * @property string type;
 * @property string utr;
 * @property string tradingName;
 * @property string companyUtr;
 * @property string companyNumber;
 * @property boolean vatRegistered;
 * @property string vatNumber;
 * @property double vatRate;
 * @property boolean reverseChargeVAT;
 */

class CisDetails extends ActiveRecord
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
        return Table::CIS_DETAILS;
    }
}
