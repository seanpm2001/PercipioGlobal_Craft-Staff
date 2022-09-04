<?php

namespace percipiolondon\staff\elements\db;

use craft\elements\db\ElementQuery;
use craft\helpers\Db;

class PayRunEntryQuery extends ElementQuery
{
    public $staffologyId;
    public $payRunId;
    public $employerId;
    public $employeeId;
    public $isClosed;
    public $isYtd;
    public $taxYear;

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

    public function isClosed($value)
    {
        $this->isClosed = $value;
        return $this;
    }

    public function isYtd($value)
    {
        $this->isYtd = $value;
        return $this;
    }

    public function taxYear($value)
    {
        $this->taxYear = $value;
        return $this;
    }

    protected function beforePrepare(): bool
    {
        $this->joinElementTable('staff_pay_run_entries');

        $this->query->select([
            'staff_pay_run_entries.payRunId',
            'staff_pay_run_entries.employerId',
            'staff_pay_run_entries.employeeId',
            'staff_pay_run_entries.staffologyId',
            'staff_pay_run_entries.taxYear',
            'staff_pay_run_entries.startDate',
            'staff_pay_run_entries.endDate',
            'staff_pay_run_entries.note',
            'staff_pay_run_entries.bacsSubReference',
            'staff_pay_run_entries.bacsHashcode',
            'staff_pay_run_entries.percentageOfWorkingDaysPaidAsNormal',
            'staff_pay_run_entries.workingDaysNotPaidAsNormal',
            'staff_pay_run_entries.payPeriod',
            'staff_pay_run_entries.ordinal',
            'staff_pay_run_entries.period',
            'staff_pay_run_entries.isNewStarter',
            'staff_pay_run_entries.unpaidAbsence',
            'staff_pay_run_entries.hasAttachmentOrders',
            'staff_pay_run_entries.paymentDate',
            'staff_pay_run_entries.forcedCisVatAmount',
            'staff_pay_run_entries.holidayAccrued',
            'staff_pay_run_entries.state',
            'staff_pay_run_entries.isClosed',
            'staff_pay_run_entries.manualNi',
            'staff_pay_run_entries.payrollCodeChanged',
            'staff_pay_run_entries.aeNotEnroledWarning',
            'staff_pay_run_entries.receivingOffsetPay',
            'staff_pay_run_entries.paymentAfterLearning',
            'staff_pay_run_entries.pdf',

        ]);

        if ($this->staffologyId) {
            $this->subQuery->andWhere(Db::parseParam('staff_pay_run_entries.staffologyId', $this->staffologyId));
        }

        if ($this->payRunId) {
            $this->subQuery->andWhere(Db::parseParam('staff_pay_run_entries.payRunId', $this->payRunId));
        }

        if ($this->employerId) {
            $this->subQuery->andWhere(Db::parseParam('staff_pay_run_entries.employerId', $this->employerId));
        }

        if ($this->employeeId) {
            $this->subQuery->andWhere(Db::parseParam('staff_pay_run_entries.employeeId', $this->employeeId));
        }

        if ($this->isClosed) {
            $this->subQuery->andWhere(Db::parseParam('staff_pay_run_entries.isClosed', $this->isClosed, '=', false));
        }

        if ($this->isYtd) {
            $this->subQuery->andWhere(Db::parseParam('staff_pay_run_entries.isYtd', $this->isYtd, '=', false));
        }

        if ($this->taxYear) {
            $this->subQuery->andWhere(Db::parseParam('staff_pay_run_entries.taxYear', $this->taxYear));
        }

        return parent::beforePrepare();
    }
}
