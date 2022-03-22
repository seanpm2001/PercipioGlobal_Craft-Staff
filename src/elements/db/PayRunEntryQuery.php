<?php

namespace percipiolondon\staff\elements\db;

use craft\db\Query;
use craft\db\QueryAbortedException;
use craft\db\Table;
use craft\elements\db\ElementQuery;
use craft\helpers\Db;

use yii\db\Connection;

class PayRunEntryQuery extends ElementQuery
{
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
    public $priorPayrollCodeId;
    public $payOptionsId;
    public $pensionSummaryId;
    public $totalsId;
    public $periodOverrides;
    public $totalsYtdId;
    public $totalsYtdOverrides;
    public $forcedCisVatAmount;
    public $holidayAccrued;
    public $state;
    public $isClosed;
    public $manualNi;
    public $nationalInsuranceCalculationId;
    public $payrollCodeChanged;
    public $aeNotEnroledWarning;
    public $fpsId;
    public $receivingOffsetPay;
    public $paymentAfterLearning;
    public $umbrellaPaymentId;
    public $employee;
    public $pdf;

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

    public function employeeId($value)
    {
        $this->employeeId = $value;
        return $this;
    }

    public function employerId($value)
    {
        $this->employerId = $value;
        return $this;
    }
    public function payOptionsId($value)
    {
        $this->payOptionsId = $value;
        return $this;
    }

    public function pensionSummaryId($value)
    {
        $this->pensionSummaryId = $value;
        return $this;
    }

    public function totalsId($value)
    {
        $this->totalsId = $value;
        return $this;
    }

    public function priorPayrollCodeId($value)
    {
        $this->priorPayrollCodeId = $value;
        return $this;
    }

    public function totalsYtdId($value)
    {
        $this->totalsYtdId = $value;
        return $this;
    }

    public function totalsYtdOverridesId($value)
    {
        $this->totalsYtdOverridesId = $value;
        return $this;
    }

    public function nationalInsuranceCalculationId($value)
    {
        $this->nationalInsuranceCalculationId = $value;
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

    public function periodOverrides($value)
    {
        $this->periodOverrides = $value;
        return $this;
    }

    public function forcedCisVatAmount($value)
    {
        $this->forcedCisVatAmount = $value;
        return $this;
    }

    public function holidayAccrued($value)
    {
        $this->holidayAccrued = $value;
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

    public function fpsId($value)
    {
        $this->fpsId = $value;
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

    public function umbrellaPaymentId($value)
    {
        $this->umbrellaPaymentId = $value;
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

    protected function beforePrepare(): bool
    {
        $this->joinElementTable('staff_payrun_entries');

        $this->query->select([
            'staff_payrun_entries.payRunId',
            'staff_payrun_entries.employerId',
            'staff_payrun_entries.employeeId',
            'staff_payrun_entries.payOptionsId',
            'staff_payrun_entries.priorPayrollCodeId',
            'staff_payrun_entries.totalsYtdId',
            'staff_payrun_entries.umbrellaPaymentId',
            'staff_payrun_entries.nationalInsuranceCalculationId',
            'staff_payrun_entries.pensionSummaryId',
            'staff_payrun_entries.employee',
            'staff_payrun_entries.fpsId',
            'staff_payrun_entries.staffologyId',
            'staff_payrun_entries.taxYear',
            'staff_payrun_entries.startDate',
            'staff_payrun_entries.endDate',
            'staff_payrun_entries.note',
            'staff_payrun_entries.bacsSubReference',
            'staff_payrun_entries.bacsHashcode',
            'staff_payrun_entries.percentageOfWorkingDaysPaidAsNormal',
            'staff_payrun_entries.workingDaysNotPaidAsNormal',
            'staff_payrun_entries.payPeriod',
            'staff_payrun_entries.ordinal',
            'staff_payrun_entries.period',
            'staff_payrun_entries.isNewStarter',
            'staff_payrun_entries.unpaidAbsence',
            'staff_payrun_entries.hasAttachmentOrders',
            'staff_payrun_entries.paymentDate',
            'staff_payrun_entries.totalsId',
            'staff_payrun_entries.forcedCisVatAmount',
            'staff_payrun_entries.holidayAccrued',
            'staff_payrun_entries.state',
            'staff_payrun_entries.isClosed',
            'staff_payrun_entries.manualNi',
            'staff_payrun_entries.payrollCodeChanged',
            'staff_payrun_entries.aeNotEnroledWarning',
            'staff_payrun_entries.receivingOffsetPay',
            'staff_payrun_entries.paymentAfterLearning',
            'staff_payrun_entries.pdf',

        ]);

        if ($this->staffologyId) {
            $this->subQuery->andWhere(Db::parseParam('staff_payrun_entries.staffologyId', $this->staffologyId));
        }

        if ($this->payRunId) {
            $this->subQuery->andWhere(Db::parseParam('staff_payrun_entries.payRunId', $this->payRunId));
        }

        if ($this->employerId) {
            $this->subQuery->andWhere(Db::parseParam('staff_payrun_entries.employerId', $this->employerId));
        }

        if ($this->employeeId) {
            $this->subQuery->andWhere(Db::parseParam('staff_payrun_entries.employeeId', $this->employeeId));
        }

        if ($this->isClosed) {
            $this->subQuery->andWhere(Db::parseParam('staff_payrun_entries.isClosed', $this->isClosed, '=', false));
        }

        return parent::beforePrepare();
    }
}
