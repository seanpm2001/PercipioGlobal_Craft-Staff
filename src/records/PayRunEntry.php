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
 * @property integer $noteId;
 * @property integer $priorPayrollCodeId;
 * @property string $payOptionsId;
 * @property integer $pensionSummaryId;
 * @property integer $totalsId;
 * @property integer $periodOverridesId;
 * @property integer $totalsYtdId;
 * @property integer $totalsYtdOverridesId
 * @property integer $nationalInsuranceCalculationId
 * @property integer $umbrellaPaymentId
 * @property integer $employeeId
 * @property integer $employerId
 * @property integer $payRunId
 *
 * @property integer $staffologyId
 * @property string $taxYear
 * @property \DateTime $startDate
 * @property \DateTime $endDate
 * @property string $bacsSubReference
 * @property string $bacsHashcode
 * @property double $percentageOfWorkingDaysPaidAsNormal
 * @property double $workingDaysNotPaidAsNormal
 * @property string $payPeriod
 * @property integer $ordinal
 * @property integer $period
 * @property boolean $isNewStarter
 * @property boolean $unpaidAbsence
 * @property boolean $hasAttachmentOrders
 * @property \DateTime $paymentDate
 * @property double $forcedCisVatAmount
 * @property double $holidayAccrued
 * @property string $state
 * @property boolean $isClosed
 * @property boolean $manualNi
 * @property boolean $payrollCodeChanged
 * @property boolean $aeNotEnroledWarning
 * @property string $fps
 * @property boolean $receivingOffsetPay
 * @property boolean $paymentAfterLearning
 * @property string $pdf
 */

class PayRunEntry extends ActiveRecord
{
    // Public Static Methods
    // =========================================================================

    public function rules()
    {
//        return [
//            [[
//                'payRunId',
//                'employerId',
//                'ordinal',
//                'period',
//            ], 'number', 'integerOnly' => true], [[
//                'percentageOfWorkingDaysPaidAsNormal',
//                'workingDaysNotPaidAsNormal',
//                'forcedCisVatAmount',
//                'holidayAccured',
//            ], 'double'],
//            [['startDate', 'endDate', 'paymentDate'], DateTimeValidator::class],
////            ['state', 'exists', 'targetAttribute' => ['Open', 'SubmittedForProcessing', 'Processing', 'AwaitingApproval', 'Approved', 'Finalised']],
//            [[
//                'staffologyId',
//                'taxYear',
//                'note',
//                'bacsSubReference',
//                'bacsHashcode',
//                'payPeriod',
//                'priorPayrollCode',
//                'payOptions',
//                'pensionSummary',
//                'totals',
//                'periodOverrides',
//                'totalsYtd',
//                'totalsYtdOverrides',
//                'state',
//                'nationalInsuranceCalculation',
//                'fps',
//                'umbrellaPayment',
//                'pdf',
//            ], 'string'],
//            [[
//                'unpaidAbsence',
//                'unpaidAbsence',
//                'hasAttachmentOrders',
//                'isClosed',
//                'manualNi',
//                'payrollCodeChanged',
//                'aeNotEnroledWarning',
//                'receivingOffsetPay',
//                'paymentAfterLearning',
//            ], 'boolean'],
//        ];
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
     * @return string $the table name
     */
    public static function tableName()
    {
        return Table::PAYRUN_ENTRIES;
    }
}
