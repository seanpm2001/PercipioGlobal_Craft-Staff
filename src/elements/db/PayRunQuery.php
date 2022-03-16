<?php

namespace percipiolondon\staff\elements\db;

use craft\db\Query;
use craft\db\QueryAbortedException;
use craft\db\Table;
use craft\elements\db\ElementQuery;
use craft\helpers\Db;

use yii\db\Connection;

class PayRunQuery extends ElementQuery
{
    public $staffologyId;
    public $taxYear;
    public $taxMonth;
    public $payPeriod;
    public $ordinal;
    public $period;
    public $startDate;
    public $endDate;
    public $paymentDate;
    public $employeeCount;
    public $subContractorCount;
    public $totalsId;
    public $state;
    public $isClosed;
    public $dateClosed;
    public $pdf;
    public $url;
    public $employerId;

    public function taxYear($value)
    {
        $this->taxYear = $value;
        return $this;
    }

    public function taxMonth($value)
    {
        $this->taxMonth = $value;
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

    public function paymentDate($value)
    {
        $this->paymentDate = $value;
        return $this;
    }

    public function employeeCount($value)
    {
        $this->employeeCount = $value;
        return $this;
    }

    public function subContractorCount($value)
    {
        $this->subContractorCount = $value;
        return $this;
    }

    public function totalsId($value)
    {
        $this->totalsId = $value;
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

    public function dateClosed($value)
    {
        $this->dateClosed = $value;
        return $this;
    }

    public function pdf($value)
    {
        $this->pdf = $value;
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

        $this->joinElementTable('staff_payrun');

        $this->query->select([
            'staff_payrun.taxYear',
            'staff_payrun.taxMonth',
            'staff_payrun.payPeriod',
            'staff_payrun.ordinal',
            'staff_payrun.period',
            'staff_payrun.startDate',
            'staff_payrun.endDate',
            'staff_payrun.paymentDate',
            'staff_payrun.employeeCount',
            'staff_payrun.subContractorCount',
            'staff_payrun.totalsId',
            'staff_payrun.state',
            'staff_payrun.isClosed',
            'staff_payrun.dateClosed',
            'staff_payrun.url',
            'staff_payrun.employerId'
        ]);

        if ($this->staffologyId) {
            $this->subQuery->andWhere(Db::parseParam('staff_payrun.staffologyId', $this->staffologyId));
        }

        if ($this->employerId) {
            $this->subQuery->andWhere(Db::parseParam('staff_payrun.employerId', $this->employerId));
        }

        if ($this->isClosed) {
            $this->subQuery->andWhere(Db::parseParam('staff_payrun.isClosed', $this->isClosed));
        }

        if ($this->state) {
            $this->subQuery->andWhere(Db::parseParam('staff_payrun.state', $this->state));
        }

        return parent::beforePrepare();
    }
}
