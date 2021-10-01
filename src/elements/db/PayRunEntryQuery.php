<?php

namespace percipiolondon\craftstaff\elements\db;

use craft\db\Query;
use craft\db\QueryAbortedException;
use craft\db\Table;
use craft\elements\db\ElementQuery;
use craft\helpers\Db;

use yii\db\Connection;

class PayRunEntryQuery extends ElementQuery
{
    public $siteId;
    public $staffologyId;
    public $payRunId;
    public $employerId;
    public $employeeId;
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
    public $receivingOffsetPay;
    public $paymentAfterLearning;
    public $umbrellaPayment;
    public $employee;
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

    public function receivingOffsetPay($value)
    {
        $this->receivingOffsetPay = $value;
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

    public function employee($value)
    {
        $this->employee = $value;
        return $this;
    }

    public function pdf($value)
    {
        $this->pdf = $value;
        return $this;
    }

    public function employeeId($value)
    {
        $this->employeeId = $value;
        return $this;
    }

    protected function beforePrepare(): bool
    {
        $this->joinElementTable('staff_payrunentries');

        $this->query->select([
            'staff_payrunentries.siteId',
            'staff_payrunentries.staffologyId',
            'staff_payrunentries.payRunId',
            'staff_payrunentries.employerId',
            'staff_payrunentries.employeeId',
            'staff_payrunentries.taxYear',
            'staff_payrunentries.startDate',
            'staff_payrunentries.endDate',
            'staff_payrunentries.note',
            'staff_payrunentries.bacsSubReference',
            'staff_payrunentries.bacsHashcode',
            'staff_payrunentries.percentageOfWorkingDaysPaidAsNormal',
            'staff_payrunentries.workingDaysNotPaidAsNormal',
            'staff_payrunentries.payPeriod',
            'staff_payrunentries.ordinal',
            'staff_payrunentries.period',
            'staff_payrunentries.isNewStarter',
            'staff_payrunentries.unpaidAbsence',
            'staff_payrunentries.hasAttachmentOrders',
            'staff_payrunentries.paymentDate',
            'staff_payrunentries.priorPayrollCode',
            'staff_payrunentries.payOptions',
            'staff_payrunentries.pensionSummary',
            'staff_payrunentries.totals',
            'staff_payrunentries.periodOverrides',
            'staff_payrunentries.totalsYtd',
            'staff_payrunentries.totalsYtdOverrides',
            'staff_payrunentries.forcedCisVatAmount',
            'staff_payrunentries.holidayAccured',
            'staff_payrunentries.state',
            'staff_payrunentries.isClosed',
            'staff_payrunentries.manualNi',
            'staff_payrunentries.nationalInsuranceCalculation',
            'staff_payrunentries.payrollCodeChanged',
            'staff_payrunentries.aeNotEnroledWarning',
            'staff_payrunentries.fps',
            'staff_payrunentries.receivingOffsetPay',
            'staff_payrunentries.paymentAfterLearning',
            'staff_payrunentries.umbrellaPayment',
            'staff_payrunentries.employee',
            'staff_payrunentries.pdf',

        ]);

        if ($this->staffologyId) {
            $this->subQuery->andWhere(Db::parseParam('staff_payrunentries.staffologyId', $this->staffologyId));
        }

        if ($this->payRunId) {
            $this->subQuery->andWhere(Db::parseParam('staff_payrunentries.payRunId', $this->payRunId));
        }

        if ($this->employerId) {
            $this->subQuery->andWhere(Db::parseParam('staff_payrunentries.employerId', $this->employerId));
        }

        if ($this->employeeId) {
            $this->subQuery->andWhere(Db::parseParam('staff_payrunentries.employeeId', $this->employeeId));
        }

        if ($this->isClosed) {
            $this->subQuery->andWhere(Db::parseParam('staff_payrunentries.isClosed', $this->isClosed, '=', false));
        }

        return parent::beforePrepare();
    }
}
