<?php

namespace percipiolondon\staff\records;

use craft\db\ActiveRecord;
use percipiolondon\staff\db\Table;

/**
 * @property int $item;
 * @property int $name;
 * @property int $partnership;
 * @property int $address;
 *
 * @property string $employeeUniqueId;
 * @property string $emailStatementTo;
 * @property int $numberOfPayments;
 * @property string $displayName;
 * @property string $action;
 * @property string $type;
 * @property string $tradingName;
 * @property string $worksRef;
 * @property string $unmatchedRate;
 * @property string $utr;
 * @property string $crn;
 * @property string $nino;
 * @property string $telephone;
 * @property string $totalPaymentsUnrounded;
 * @property string $costOfMaterialsUnrounded;
 * @property string $umbrellaFee;
 * @property string $validationMsg;
 * @property string $totalPayments;
 * @property string $costOfMaterials;
 * @property string $totalDeducted;
 * @property string $matched;
 * @property string $taxTreatment;
 */

class CisSubcontractor extends ActiveRecord
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
        return Table::CIS_SUBCONTRACTOR;
    }
}
