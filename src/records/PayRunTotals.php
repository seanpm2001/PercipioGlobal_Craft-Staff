<?php

namespace percipiolondon\staff\records;

use craft\db\ActiveRecord;
use percipiolondon\staff\db\Table;

/**
 * @property double $basicPay;
 * @property double $gross;
 * @property double $grossForNi;
 * @property double $grossNotSubjectToEmployersNi;
 * @property double $grossForTax;
 * @property double $employerNi;
 * @property double $employeeNi;
 * @property double $employerNiOffPayroll;
 * @property double $realTimeClass1ANi;
 * @property double $tax;
 * @property double $netPay;
 * @property double $adjustments;
 * @property double $additions;
 * @property double $takeHomePay;
 * @property double $nonTaxOrNICPmt;
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
 * @property double $studentLoanRecovered;
 * @property double $postgradLoanRecovered;
 * @property double $pensionableEarnings;
 * @property double $pensionablePay;
 * @property double $nonTierablePay;
 * @property double $employeePensionContribution;
 * @property double $employeePensionContributionAvc;
 * @property double $employerPensionContribution;
 * @property double $empeePenContribnsNotPaid;
 * @property double $empeePenContribnsPaid;
 * @property double $attachmentOrderDeductions;
 * @property double $cisDeduction;
 * @property double $cisVat;
 * @property double $cisUmbrellaFee;
 * @property double $cisUmbrellaFeePostTax;
 * @property double $pbik;
 * @property int $mapsMiles;
 * @property double $umbrellaFee;
 * @property double $appLevyDeduction;
 * @property double $paymentAfterLeaving;
 * @property double $taxOnPaymentAfterLeaving;
 * @property int $nilPaid;
 * @property int $leavers;
 * @property int $starters;
 * @property double $totalCost;
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
    public static function tableName()
    {
        return Table::UMBRELLA_SETTINGS;
    }
}