<?php

namespace percipiolondon\staff\elements\db;

use craft\elements\db\ElementQuery;
use craft\helpers\Db;

class BenefitVariantDentalQuery extends ElementQuery
{
    public $employeeId;
    public $policyId;

    public function employeeId($value)
    {
        $this->employeeId = $value;
        return $this;
    }

    public function policyId($value)
    {
        $this->policyId = $value;
        return $this;
    }

    protected function beforePrepare(): bool
    {
        $this->joinElementTable('staff_benefit_variant_dental');

        $this->query->select([
            'staff_benefit_variant_dental.name',
            'staff_benefit_variant_dental.trsId',
            'staff_benefit_variant_dental.policyId',
//            'staff_benefit_trs.title AS trsTitle',
//            'staff_benefit_trs.monetaryValue AS trsMonetaryValue',
//            'staff_benefit_trs.startDate AS trsStartDate',
//            'staff_benefit_trs.endDate AS trsEndDate',
        ]);

        if ($this->policyId) {
            $this->subQuery->andWhere(Db::parseParam('staff_benefit_variant_dental.policyId', $this->policyId));
        }

//        $this->leftJoin('staff_benefit_trs', 'staff_benefit_variant_dental.trsId = staff_benefit_trs.id');

        return parent::beforePrepare();
    }
}
