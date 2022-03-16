<?php

namespace percipiolondon\staff\elements\db;

use craft\db\Query;
use craft\db\QueryAbortedException;
use craft\db\Table;
use craft\elements\db\ElementQuery;
use craft\helpers\Db;

use yii\db\Connection;

class EmployerQuery extends ElementQuery
{
    public $slug;
    public $siteId;
    public $staffologyId;
    public $name;
    public $logoUrl;
    public $crn;
    public $defaultPayOptionsId;
    public $addressId;
    public $startYear;
    public $currentYear;
    public $employeeCount;

    public function slug($value)
    {
        $this->slug = $value;
        return $this;
    }

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

    public function name($value)
    {
        $this->name = $value;
        return $this;
    }

    public function logoUrl($value)
    {
        $this->logoUrl = $value;
        return $this;
    }

    public function crn($value)
    {
        $this->crn = $value;
        return $this;
    }

    public function defaultPayOptionsId($value)
    {
        $this->defaultPayOptionsId = $value;
        return $this;
    }

    public function addressId($value)
    {
        $this->addressId = $value;
        return $this;
    }

    public function startYear($value)
    {
        $this->startYear = $value;
        return $this;
    }

    public function currentYear($value)
    {
        $this->currentYear = $value;
        return $this;
    }

    public function employeeCount($value)
    {
        $this->employeeCount = $value;
        return $this;
    }

    protected function beforePrepare(): bool
    {
        $this->joinElementTable('staff_employers');

        $this->query->select([
            'staff_employers.staffologyId',
            'staff_employers.addressId',
            'staff_employers.bankDetailsId',
            'staff_employers.defaultPayOptionsId',
            'staff_employers.hmrcDetailsId',
            'staff_employers.rtiSubmissionSettingsId',
            'staff_employers.autoEnrolmentSettingsId',
            'staff_employers.leaveSettingsId',
            'staff_employers.settingsId',
            'staff_employers.umbrellaSettingsId',
            'staff_employers.name',
            'staff_employers.crn',
            'staff_employers.logoUrl',
            'staff_employers.alternativeId',
            'staff_employers.bankPaymentsCsvFormat',
            'staff_employers.bacsServiceUserNumber',
            'staff_employers.bacsBureauNumber',
            'staff_employers.rejectInvalidBankDetails',
            'staff_employers.bankPaymentsReferenceFormat',
            'staff_employers.useTenantRtiSubmissionSettings',
            'staff_employers.employeeCount',
            'staff_employers.subcontractorCount',
            'staff_employers.startYear',
            'staff_employers.currentYear',
            'staff_employers.supportAccessEnabled',
            'staff_employers.archived',
            'staff_employers.canUseBureauFeatures',
            'staff_employers.sourceSystemId',
            'staff_employers.slug',
        ]);

        if ($this->staffologyId) {
            $this->subQuery->andWhere(Db::parseParam('staff_employers.staffologyId', $this->staffologyId));
        }

        if ($this->crn) {
            $this->subQuery->andWhere(Db::parseParam('staff_employers.crn', $this->crn));
        }

        return parent::beforePrepare();
    }
}
