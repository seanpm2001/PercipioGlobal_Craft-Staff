<?php
/**
 * staff-management plugin for Craft CMS 3.x
 *
 * Craft Staff Management provides an HR solution for payroll and benefits
 *
 * @link      http://percipio.london
 * @copyright Copyright (c) 2021 Percipio
 */

namespace percipiolondon\staff\records;

use percipiolondon\staff\Staff;

use Craft;
use craft\db\ActiveRecord;
use percipiolondon\staff\db\Table;

/**
 * PayRun Record
 *
 * ActiveRecord is the base class for classes representing relational data in terms of objects.
 *
 * Active Record implements the [Active Record design pattern](http://en.wikipedia.org/wiki/Active_record).
 * The premise behind Active Record is that an individual [[ActiveRecord]] object is associated with a specific
 * row in a database table. The object's attributes are mapped to the columns of the corresponding table.
 * Referencing an Active Record attribute is equivalent to accessing the corresponding table column for that record.
 *
 * http://www.yiiframework.com/doc-2.0/guide-db-active-record.html
 *
 * @author    Percipio
 * @package   Staff
 * @since     1.0.0-alpha.1
 */

/**
 * PayRun record
 * @property int $siteId;
 * @property string $staffologyId;
 * @property string $taxYear;
 * @property int $taxMonth;
 * @property string $payPeriod;
 * @property int $ordinal;
 * @property int $period;
 * @property \DateTime $startDate;
 * @property \DateTime $endDate;
 * @property \DateTime $paymentDate;
 * @property int $employeeCount;
 * @property int $subContractorCount;
 * @property integer $totals;
 * @property string $state;
 * @property boolean $isClosed;
 * @property \DateTime $dateClosed;
 * @property string $pdf
 * @property string $url
 * @property int $employerId
 */

class PayRun extends ActiveRecord
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
    public static function tableName()
    {
        return Table::STAFF_PAYRUN;
    }
}
