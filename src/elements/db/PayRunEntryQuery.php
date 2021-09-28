<?php

namespace percipiolondon\craftstaff\elements\db;

use craft\elements\db\ElementQuery;
use percipiolondon\companymanagement\elements\Department;

class PayRunEntryQuery extends ElementQuery
{
    public $siteId;
    public $staffologyId;
    public $payRunId;
    public $employerId;
    public $taxYear;
    public $startDate;
    public $endDate;
    public $note;
    public $bacsSubReference;
    public $bacsHashcode;
    public $percentageOfWorkingDaysPaidAsNormal;
    public $workingDaysNotPaidAsNormal;
    public $payPeriod;
    public $ordinal;
    public $period;
    public $isNewStarter;
    public $unpaidAbsence;
    public $hasAttachmentOrders;
    public $paymentDate;
    public $priorPayrollCode;
    public $payOptions;
    public $pensionSummary;
    public $totals;
    public $periodOverrides;
    public $totalsYtd;
    public $totalsYtdOverrides;
    public $forcedCisVatAmount;
    public $holidayAccured;
    public $state;
    public $isClosed;
    public $manualNi;
    public $nationalInsuranceCalculation;
    public $payrollCodeChanged;
    public $aeNotEnroledWarning;
    public $fps;
    public $recievingOffsetPay;
    public $paymentAfterLearning;
    public $umbrellaPayment;
    public $pdf;

    public function siteId($value)
    {
        $this->siteId = $value;
        return $this;
    }

    public function staffologyId($value)
    {
        $this->staffologyId = $value;
        return $this;
    }

    public function payRunId($value)
    {
        $this->payRunId = $value;
        return $this;
    }

    public function employerId($value)
    {
        $this->employerId = $value;
        return $this;
    }

    public function taxYear($value)
    {
        $this->taxYear = $value;
        return $this;
    }

    public function startDate($value)
    {
        $this->startDate = $value;
        return $this;
    }

    public function endDate($value)
    {
        $this->endDate = $value;
        return $this;
    }

    public function note($value)
    {
        $this->note = $value;
        return $this;
    }

    public function bacsSubReference($value)
    {
        $this->bacsSubReference = $value;
        return $this;
    }

    public function bacsHashcode($value)
    {
        $this->bacsHashcode = $value;
        return $this;
    }

    public function percentageOfWorkingDaysPaidAsNormal($value)
    {
        $this->percentageOfWorkingDaysPaidAsNormal = $value;
        return $this;
    }

    public function workingDaysNotPaidAsNormal($value)
    {
        $this->workingDaysNotPaidAsNormal = $value;
        return $this;
    }

    public function payPeriod($value)
    {
        $this->payPeriod = $value;
        return $this;
    }

    public function ordinal($value)
    {
        $this->ordinal = $value;
        return $this;
    }

    public function period($value)
    {
        $this->period = $value;
        return $this;
    }

    public function isNewStarter($value)
    {
        $this->isNewStarter = $value;
        return $this;
    }

    public function unpaidAbsence($value)
    {
        $this->unpaidAbsence = $value;
        return $this;
    }

    public function hasAttachmentOrders($value)
    {
        $this->hasAttachmentOrders = $value;
        return $this;
    }

    public function paymentDate($value)
    {
        $this->paymentDate = $value;
        return $this;
    }

    public function priorPayrollCode($value)
    {
        $this->priorPayrollCode = $value;
        return $this;
    }

    public function payOptions($value)
    {
        $this->payOptions = $value;
        return $this;
    }

    public function pensionSummary($value)
    {
        $this->pensionSummary = $value;
        return $this;
    }

    public function totals($value)
    {
        $this->totals = $value;
        return $this;
    }

    public function periodOverrides($value)
    {
        $this->periodOverrides = $value;
        return $this;
    }

    public function totalsYtd($value)
    {
        $this->totalsYtd = $value;
        return $this;
    }

    public function totalsYtdOverrides($value)
    {
        $this->totalsYtdOverrides = $value;
        return $this;
    }

    public function forcedCisVatAmount($value)
    {
        $this->forcedCisVatAmount = $value;
        return $this;
    }

    public function holidayAccured($value)
    {
        $this->holidayAccured = $value;
        return $this;
    }

    public function state($value)
    {
        $this->state = $value;
        return $this;
    }

    public function isClosed($value)
    {
        $this->isClosed = $value;
        return $this;
    }

    public function manualNi($value)
    {
        $this->manualNi = $value;
        return $this;
    }

    public function nationalInsuranceCalculation($value)
    {
        $this->nationalInsuranceCalculation = $value;
        return $this;
    }

    public function payrollCodeChanged($value)
    {
        $this->payrollCodeChanged = $value;
        return $this;
    }

    public function aeNotEnroledWarning($value)
    {
        $this->aeNotEnroledWarning = $value;
        return $this;
    }

    public function fps($value)
    {
        $this->fps = $value;
        return $this;
    }

    public function recievingOffsetPay($value)
    {
        $this->recievingOffsetPay = $value;
        return $this;
    }

    public function paymentAfterLearning($value)
    {
        $this->paymentAfterLearning = $value;
        return $this;
    }

    public function umbrellaPayment($value)
    {
        $this->umbrellaPayment = $value;
        return $this;
    }

    public function pdf($value)
    {
        $this->pdf = $value;
        return $this;
    }

    protected function beforePrepare(): bool
    {
        $this->joinElementTable('staff_payrunentry');

        $this->query->select([
            'staff_payrunentry.siteId',
            'staff_payrunentry.staffologyId',
            'staff_payrunentry.payRunId',
            'staff_payrunentry.employerId',
            'staff_payrunentry.taxYear',
            'staff_payrunentry.startDate',
            'staff_payrunentry.endDate',
            'staff_payrunentry.note',
            'staff_payrunentry.bacsSubReference',
            'staff_payrunentry.bacsHashcode',
            'staff_payrunentry.percentageOfWorkingDaysPaidAsNormal',
            'staff_payrunentry.workingDaysNotPaidAsNormal',
            'staff_payrunentry.payPeriod',
            'staff_payrunentry.ordinal',
            'staff_payrunentry.period',
            'staff_payrunentry.isNewStarter',
            'staff_payrunentry.unpaidAbsence',
            'staff_payrunentry.hasAttachmentOrders',
            'staff_payrunentry.paymentDate',
            'staff_payrunentry.priorPayrollCode',
            'staff_payrunentry.payOptions',
            'staff_payrunentry.pensionSummary',
            'staff_payrunentry.totals',
            'staff_payrunentry.periodOverrides',
            'staff_payrunentry.totalsYtd',
            'staff_payrunentry.totalsYtdOverrides',
            'staff_payrunentry.forcedCisVatAmount',
            'staff_payrunentry.holidayAccured',
            'staff_payrunentry.state',
            'staff_payrunentry.isClosed',
            'staff_payrunentry.manualNi',
            'staff_payrunentry.nationalInsuranceCalculation',
            'staff_payrunentry.payrollCodeChanged',
            'staff_payrunentry.aeNotEnroledWarning',
            'staff_payrunentry.fps',
            'staff_payrunentry.recievingOffsetPay',
            'staff_payrunentry.paymentAfterLearning',
            'staff_payrunentry.umbrellaPayment',
            'staff_payrunentry.pdf',

        ]);

        return parent::beforePrepare();
    }
}
