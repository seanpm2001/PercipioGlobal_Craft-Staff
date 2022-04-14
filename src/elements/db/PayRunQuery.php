<?php

namespace percipiolondon\staff\elements\db;

use craft\elements\db\ElementQuery;
use craft\helpers\Db;

class PayRunQuery extends ElementQuery
{
    public $staffologyId;
    public $state;
    public $taxYear;
    public $isClosed;
    public $url;
    public $employerId;

    public function state($value)
    {
        $this->state = $value;
        return $this;
    }

    public function taxYear($value)
    {
        $this->taxYear = $value;
        return $this;
    }

    public function isClosed($value)
    {
        $this->isClosed = $value;
        return $this;
    }

    public function url($value)
    {
        $this->url = $value;
        return $this;
    }

    public function employerId($value)
    {
        $this->employerId = $value;
        return $this;
    }

    protected function beforePrepare(): bool
    {
        $this->joinElementTable('staff_pay_run');

        $this->query->select([
            'staff_pay_run.taxYear',
            'staff_pay_run.taxMonth',
            'staff_pay_run.payPeriod',
            'staff_pay_run.ordinal',
            'staff_pay_run.period',
            'staff_pay_run.startDate',
            'staff_pay_run.endDate',
            'staff_pay_run.paymentDate',
            'staff_pay_run.employeeCount',
            'staff_pay_run.subContractorCount',
            'staff_pay_run.state',
            'staff_pay_run.isClosed',
            'staff_pay_run.dateClosed',
            'staff_pay_run.url',
            'staff_pay_run.employerId',
        ]);

        if ($this->employerId) {
            $this->subQuery->andWhere(Db::parseParam('staff_pay_run.employerId', $this->employerId));
        }

        if ($this->isClosed) {
            $this->subQuery->andWhere(Db::parseParam('staff_pay_run.isClosed', $this->isClosed));
        }

        if ($this->state) {
            $this->subQuery->andWhere(Db::parseParam('staff_pay_run.state', $this->state));
        }

        if ($this->url) {
            $this->subQuery->andWhere(Db::parseParam('staff_pay_run.url', $this->url));
        }

        if ($this->taxYear) {
            $this->subQuery->andWhere(Db::parseParam('staff_pay_run.taxYear', $this->taxYear));
        }

        return parent::beforePrepare();
    }
}
