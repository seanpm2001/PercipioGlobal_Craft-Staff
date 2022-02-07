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

use craft\validators\DateTimeValidator;
use percipiolondon\staff\Staff;

use Craft;
use craft\db\ActiveRecord;
use percipiolondon\staff\db\Table;

/**
 * PayRunEntry Record
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
 *
 *
 * PayRunEntry record
 * @property string $siteId;
 * @property string $staffologyId;
 * @property int $payRunId;
 * @property int $employerId;
 * @property int $taxYear;
 * @property \DateTime $startDate;
 * @property \DateTime $endDate;
 * @property string $note
 * @property string $bacsSubReference
 * @property string $bacsHashcode
 * @property double $percentageOfWorkingDaysPaidAsNormal
 * @property double $workingDaysNotPaidAsNormal
 * @property string $payPeriod
 * @property int $ordinal
 * @property int $period
 * @property boolean $isNewStarter
 * @property boolean $unpaidAbsence
 * @property boolean $hasAttachmentOrders
 * @property \DateTime $paymentDate
 * @property string $priorPayrollCode
 * @property integer $payOptions
 * @property integer $pensionSummary
 * @property integer $totals
 * @property integer $periodOverrides
 * @property integer $totalsYtd
 * @property integer $totalsYtdOverrides
 * @property double $forcedCisVatAmount
 * @property double $holidayAccured
 * @property string $state
 * @property boolean $isClosed
 * @property boolean $manualNi
 * @property integer $nationalInsuranceCalculation
 * @property boolean $payrollCodeChanged
 * @property boolean $aeNotEnroledWarning
 * @property string $fps
 * @property boolean $receivingOffsetPay
 * @property boolean $paymentAfterLearning
 * @property integer $umbrellaPayment
 * @property integer $employee
 * @property int $employeeId
 * @property string $pdf
 */

class PayRunEntry extends ActiveRecord
{
    // Public Static Methods
    // =========================================================================

    public function rules()
    {
        return [
            [[
                'siteId',
                'payRunId',
                'employerId',
                'ordinal',
                'period',
            ], 'number', 'integerOnly' => true], [[
                'percentageOfWorkingDaysPaidAsNormal',
                'workingDaysNotPaidAsNormal',
                'forcedCisVatAmount',
                'holidayAccured',
            ], 'double'],
            [['startDate', 'endDate', 'paymentDate'], DateTimeValidator::class],
//            ['state', 'exists', 'targetAttribute' => ['Open', 'SubmittedForProcessing', 'Processing', 'AwaitingApproval', 'Approved', 'Finalised']],
            [[
                'staffologyId',
                'taxYear',
                'note',
                'bacsSubReference',
                'bacsHashcode',
                'payPeriod',
                'priorPayrollCode',
                'payOptions',
                'pensionSummary',
                'totals',
                'periodOverrides',
                'totalsYtd',
                'totalsYtdOverrides',
                'state',
                'nationalInsuranceCalculation',
                'fps',
                'umbrellaPayment',
                'pdf',
            ], 'string'],
            [[
                'unpaidAbsence',
                'unpaidAbsence',
                'hasAttachmentOrders',
                'isClosed',
                'manualNi',
                'payrollCodeChanged',
                'aeNotEnroledWarning',
                'receivingOffsetPay',
                'paymentAfterLearning',
            ], 'boolean'],
        ];
    }

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
        return Table::STAFF_PAYRUNENTRIES;
    }
}
