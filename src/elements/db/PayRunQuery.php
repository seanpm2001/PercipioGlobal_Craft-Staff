<?php

namespace percipiolondon\craftstaff\elements\db;

use craft\elements\db\ElementQuery;
use percipiolondon\companymanagement\elements\Department;

class PayRunQuery extends ElementQuery
{
    public $siteId;
    public $staffologyId;
    public $taxYear;
    public $taxMonth;
    public $payPeriod;
    public $ordinal;
    public $period;
    public $startDate;
    public $endDate;
    public $employeeCount;
    public $subContractorCount;
    public $totals;
    public $state;
    public $isClosed;
    public $dateClosed;
    public $pdf;
    public $url;
    public $employerId;

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

    public function totals($value)
    {
        $this->totals = $value;
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
            'staff_payrun.siteId',
            'staff_payrun.staffologyId',
            'staff_payrun.taxYear',
            'staff_payrun.taxMonth',
            'staff_payrun.payPeriod',
            'staff_payrun.ordinal',
            'staff_payrun.period',
            'staff_payrun.startDate',
            'staff_payrun.endDate',
            'staff_payrun.employeeCount',
            'staff_payrun.subContractorCount',
            'staff_payrun.totals',
            'staff_payrun.state',
            'staff_payrun.isClosed',
            'staff_payrun.dateClosed',
            'staff_payrun.pdf',
            'staff_payrun.url',
            'staff_payrun.employerId'
        ]);

        return parent::beforePrepare();
    }
}