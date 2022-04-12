<?php

namespace percipiolondon\staff\records;

use craft\db\ActiveRecord;
use percipiolondon\staff\db\Table;

/**
 * @property string $basicPay;
 * @property string $gross;
 * @property string $grossForNi;
 * @property string $grossNotSubjectToEmployersNi;
 * @property string $grossForTax;
 * @property string $employerNi;
 * @property string $employeeNi;
 * @property double $employerNiOffPayroll;
 * @property double $realTimeClass1ANi;
 * @property string $tax;
 * @property string $netPay;
 * @property string $adjustments;
 * @property string $additions;
 * @property string $takeHomePay;
 * @property string $nonTaxOrNICPmt;
 * @property double $itemsSubjectToClass1NIC;
 * @property double $dednsFromNetPay;
 * @property double $tcp_Tcls;
 * @property double $tcp_Pp;
 * @property double $tcp_Op;
 * @property double $flexiDd_Death;
 * @property double $flexiDd_Death_NonTax;
 * @property double $flexiDd_Pension;
 * @property double $flexiDd_Pension_NonTax;
 * @property double $smp;
 * @property double $spp;
 * @property double $sap;
 * @property double $shpp;
 * @property double $spbp;
 * @property double $ssp;
 * @property string $studentLoanRecovered;
 * @property string $postgradLoanRecovered;
 * @property string $pensionableEarnings;
 * @property string $pensionablePay;
 * @property string $nonTierablePay;
 * @property string $employeePensionContribution;
 * @property string $employeePensionContributionAvc;
 * @property string $employerPensionContribution;
 * @property string $empeePenContribnsNotPaid;
 * @property string $empeePenContribnsPaid;
 * @property string $attachmentOrderDeductions;
 * @property string $cisDeduction;
 * @property string $cisVat;
 * @property string $cisUmbrellaFee;
 * @property string $cisUmbrellaFeePostTax;
 * @property double $pbik;
 * @property int $mapsMiles;
 * @property string $umbrellaFee;
 * @property double $appLevyDeduction;
 * @property double $paymentAfterLeaving;
 * @property double $taxOnPaymentAfterLeaving;
 * @property int $nilPaid;
 * @property int $leavers;
 * @property int $starters;
 * @property string $totalCost;
 */

class PayRunTotals extends ActiveRecord
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
        return Table::PAYRUN_TOTALS;
    }
}
