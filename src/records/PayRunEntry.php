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
 * @property int $priorPayrollCodeId;
 * @property string $payOptionsId;
 * @property int $pensionSummaryId;
 * @property int $totalsId;
 * @property int $periodOverridesId;
 * @property int $totalsYtdId;
 * @property int $totalsYtdOverridesId
 * @property int $nationalInsuranceCalculationId
 * @property int $umbrellaPaymentId
 * @property int $employeeId
 * @property int $employerId
 * @property int $payRunId
 *
 * @property int $staffologyId
 * @property string $taxYear
 * @property \DateTime $startDate
 * @property \DateTime $endDate
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
 * @property double $forcedCisVatAmount
 * @property double $holidayAccrued
 * @property string $state
 * @property boolean $isClosed
 * @property boolean $manualNi
 * @property boolean $payrollCodeChanged
 * @property boolean $aeNotEnroledWarning
 * @property boolean $receivingOffsetPay
 * @property boolean $paymentAfterLearning
 * @property string $pdf
 */

class PayRunEntry extends ActiveRecord
{
    // Public Static Methods
    // =========================================================================

//    public function rules()
//    {
//        return [
//            [[
//                'payRunId',
//                'employerId',
//                'ordinal',
//                'period',
//            ], 'number', 'intOnly' => true], [[
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
//    }

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
