<?php

namespace percipiolondon\staff\services;

use Craft;
use craft\base\Component;
use percipiolondon\staff\helpers\Logger;
use percipiolondon\staff\helpers\Security as SecurityHelper;
use percipiolondon\staff\records\PayRunTotals;
use yii\db\Exception;

class Totals extends Component
{
    public function savePayRunTotals(array $totals, int $payRunId = null, bool $isYtd = false): PayRunTotals
    {
        $record = PayRunTotals::findOne(['payRunId' => $payRunId]);

        if (!$record) {
            $record = new PayRunTotals();
        }

        $record->payRunId = $payRunId;
        $record->isYtd = $isYtd ?? false;

        return $this->_saveRecord($record, $totals);
    }

    public function savePayRunEntryTotals(array $totals, int $payRunEntryId = null, bool $isYtd = false): PayRunTotals
    {
        $record = PayRunTotals::findOne(['payRunEntryId' => $payRunEntryId]);

        if (!$record) {
            $record = new PayRunTotals();
        }

        $record->payRunEntryId = $payRunEntryId;
        $record->isYtd = $isYtd ?? false;

        return $this->_saveRecord($record, $totals);
    }

    public function parseTotals(array $totals): array
    {
        $totals['basicPay'] = SecurityHelper::decrypt($totals['basicPay'] ?? '');
        $totals['gross'] = SecurityHelper::decrypt($totals['gross'] ?? '');
        $totals['grossForNi'] = SecurityHelper::decrypt($totals['grossForNi'] ?? '');
        $totals['grossNotSubjectToEmployersNi'] = SecurityHelper::decrypt($totals['grossNotSubjectToEmployersNi'] ?? '');
        $totals['grossForTax'] = SecurityHelper::decrypt($totals['grossForTax'] ?? '');
        $totals['employerNi'] = SecurityHelper::decrypt($totals['employerNi'] ?? '');
        $totals['employeeNi'] = SecurityHelper::decrypt($totals['employeeNi'] ?? '');
        $totals['tax'] = SecurityHelper::decrypt($totals['tax'] ?? '');
        $totals['netPay'] = SecurityHelper::decrypt($totals['netPay'] ?? '');
        $totals['adjustments'] = SecurityHelper::decrypt($totals['adjustments'] ?? '');
        $totals['additions'] = SecurityHelper::decrypt($totals['additions'] ?? '');
        $totals['takeHomePay'] = SecurityHelper::decrypt($totals['takeHomePay'] ?? '');
        $totals['nonTaxOrNICPmt'] = SecurityHelper::decrypt($totals['nonTaxOrNICPmt'] ?? '');
        $totals['studentLoanRecovered'] = SecurityHelper::decrypt($totals['studentLoanRecovered'] ?? '');
        $totals['postgradLoanRecovered'] = SecurityHelper::decrypt($totals['postgradLoanRecovered'] ?? '');
        $totals['pensionableEarnings'] = SecurityHelper::decrypt($totals['pensionableEarnings'] ?? '');
        $totals['pensionablePay'] = SecurityHelper::decrypt($totals['pensionablePay'] ?? '');
        $totals['nonTierablePay'] = SecurityHelper::decrypt($totals['nonTierablePay'] ?? '');
        $totals['employeePensionContribution'] = SecurityHelper::decrypt($totals['employeePensionContribution'] ?? '');
        $totals['employeePensionContributionAvc'] = SecurityHelper::decrypt($totals['employeePensionContributionAvc'] ?? '');
        $totals['employerPensionContribution'] = SecurityHelper::decrypt($totals['employerPensionContribution'] ?? '');
        $totals['empeePenContribnsNotPaid'] = SecurityHelper::decrypt($totals['empeePenContribnsNotPaid'] ?? '');
        $totals['empeePenContribnsPaid'] = SecurityHelper::decrypt($totals['empeePenContribnsPaid'] ?? '');
        $totals['attachmentOrderDeductions'] = SecurityHelper::decrypt($totals['attachmentOrderDeductions'] ?? '');
        $totals['cisDeduction'] = SecurityHelper::decrypt($totals['cisDeduction'] ?? '');
        $totals['cisVat'] = SecurityHelper::decrypt($totals['cisVat'] ?? '');
        $totals['cisUmbrellaFee'] = SecurityHelper::decrypt($totals['cisUmbrellaFee'] ?? '');
        $totals['cisUmbrellaFeePostTax'] = SecurityHelper::decrypt($totals['cisUmbrellaFeePostTax'] ?? '');
        $totals['umbrellaFee'] = SecurityHelper::decrypt($totals['umbrellaFee'] ?? '');
        $totals['totalCost'] = SecurityHelper::decrypt($totals['totalCost'] ?? '');

        return $totals;
    }

    private function _saveRecord(PayRunTotals $record, array $totals): ?PayRunTotals
    {
        $record->basicPay = SecurityHelper::encrypt($totals['basicPay'] ?? '');
        $record->gross = SecurityHelper::encrypt($totals['gross'] ?? '');
        $record->grossForNi = SecurityHelper::encrypt($totals['grossForNi'] ?? '');
        $record->grossNotSubjectToEmployersNi = SecurityHelper::encrypt($totals['grossNotSubjectToEmployersNi'] ?? '');
        $record->grossForTax = SecurityHelper::encrypt($totals['grossForTax'] ?? '');
        $record->employerNi = SecurityHelper::encrypt($totals['employerNi'] ?? '');
        $record->employeeNi = SecurityHelper::encrypt($totals['employeeNi'] ?? '');
        $record->employerNiOffPayroll = $totals['employerNiOffPayroll'] ?? null;
        $record->realTimeClass1ANi = $totals['realTimeClass1ANi'] ?? null;
        $record->tax = SecurityHelper::encrypt($totals['tax'] ?? '');
        $record->netPay = SecurityHelper::encrypt($totals['netPay'] ?? '');
        $record->adjustments = SecurityHelper::encrypt($totals['adjustments'] ?? '');
        $record->additions = SecurityHelper::encrypt($totals['additions'] ?? '');
        $record->takeHomePay = SecurityHelper::encrypt($totals['takeHomePay'] ?? '');
        $record->nonTaxOrNICPmt = SecurityHelper::encrypt($totals['nonTaxOrNICPmt'] ?? '');
        $record->itemsSubjectToClass1NIC = $totals['itemsSubjectToClass1NIC'] ?? null;
        $record->dednsFromNetPay = $totals['dednsFromNetPay'] ?? null;
        $record->tcp_Tcls = $totals['tcp_Tcls'] ?? null;
        $record->tcp_Pp = $totals['tcp_Pp'] ?? null;
        $record->tcp_Op = $totals['tcp_Op'] ?? null;
        $record->flexiDd_Death = $totals['flexiDd_Death'] ?? null;
        $record->flexiDd_Death_NonTax = $totals['flexiDd_Death_NonTax'] ?? null;
        $record->flexiDd_Pension = $totals['flexiDd_Pension'] ?? null;
        $record->flexiDd_Pension_NonTax = $totals['flexiDd_Pension_NonTax'] ?? null;
        $record->smp = $totals['smp'] ?? null;
        $record->spp = $totals['spp'] ?? null;
        $record->sap = $totals['sap'] ?? null;
        $record->shpp = $totals['shpp'] ?? null;
        $record->spbp = $totals['spbp'] ?? null;
        $record->ssp = $totals['ssp'] ?? null;
        $record->studentLoanRecovered = SecurityHelper::encrypt($totals['studentLoanRecovered'] ?? '');
        $record->postgradLoanRecovered = SecurityHelper::encrypt($totals['postgradLoanRecovered'] ?? '');
        $record->pensionableEarnings = SecurityHelper::encrypt($totals['pensionableEarnings'] ?? '');
        $record->pensionablePay = SecurityHelper::encrypt($totals['pensionablePay'] ?? '');
        $record->nonTierablePay = SecurityHelper::encrypt($totals['nonTierablePay'] ?? '');
        $record->employeePensionContribution = SecurityHelper::encrypt($totals['employeePensionContribution'] ?? '');
        $record->employeePensionContributionAvc = SecurityHelper::encrypt($totals['employeePensionContributionAvc'] ?? '');
        $record->employerPensionContribution = SecurityHelper::encrypt($totals['employerPensionContribution'] ?? '');
        $record->empeePenContribnsNotPaid = SecurityHelper::encrypt($totals['empeePenContribnsNotPaid'] ?? '');
        $record->empeePenContribnsPaid = SecurityHelper::encrypt($totals['empeePenContribnsPaid'] ?? '');
        $record->attachmentOrderDeductions = SecurityHelper::encrypt($totals['attachmentOrderDeductions'] ?? '');
        $record->cisDeduction = SecurityHelper::encrypt($totals['cisDeduction'] ?? '');
        $record->cisVat = SecurityHelper::encrypt($totals['cisVat'] ?? '');
        $record->cisUmbrellaFee = SecurityHelper::encrypt($totals['cisUmbrellaFee'] ?? '');
        $record->cisUmbrellaFeePostTax = SecurityHelper::encrypt($totals['cisUmbrellaFeePostTax'] ?? '');
        $record->pbik = $totals['pbik'] ?? null;
        $record->mapsMiles = $totals['mapsMiles'] ?? null;
        $record->umbrellaFee = SecurityHelper::encrypt($totals['umbrellaFee'] ?? '');
        $record->appLevyDeduction = $totals['appLevyDeduction'] ?? null;
        $record->paymentAfterLeaving = $totals['paymentAfterLeaving'] ?? null;
        $record->taxOnPaymentAfterLeaving = $totals['taxOnPaymentAfterLeaving'] ?? null;
        $record->nilPaid = $totals['nilPaid'] ?? null;
        $record->leavers = $totals['leavers'] ?? null;
        $record->starters = $totals['starters'] ?? null;
        $record->totalCost = SecurityHelper::encrypt($totals['totalCost'] ?? '');

        $success = $record->save();

        if ($success) {
            return $record;
        }

        $errors = '';

        foreach ($record->errors as $err) {
            $errors .= implode(',', $err);
        }

        $logger = new Logger();
        $logger->stdout($errors . PHP_EOL, $logger::FG_RED);
        Craft::error($record->errors, __METHOD__);

        return null;
    }
}
