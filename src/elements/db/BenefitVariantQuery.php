<?php

namespace percipiolondon\staff\elements\db;

use craft\db\Query;
use craft\elements\db\ElementQuery;
use craft\helpers\Db;

class BenefitVariantQuery extends ElementQuery
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
        $this->joinElementTable('staff_benefit_variant');
        $this->query->select([
            'staff_benefit_variant.name',
            'staff_benefit_variant.policyId',
        ]);


        // ---- TEST 1 -----
//        $this->query->select([
//            'staff_benefit_variant_dental.name',
//            'staff_benefit_variant_dental.trsId',
//            'staff_benefit_variant_dental.policyId',
//            //gcic
//            'staff_benefit_variant_gcic.name',
//            'staff_benefit_variant_gcic.trsId',
//            'staff_benefit_variant_gcic.policyId',
//            'staff_benefit_variant_gcic.rateReviewGuaranteeDate',
//            'staff_benefit_variant_gcic.costingBasis',
//            'staff_benefit_variant_gcic.unitRate',
//            'staff_benefit_variant_gcic.unitRateSuffix',
//            'staff_benefit_variant_gcic.freeCoverLevelAutomaticAcceptanceLimit',
//            'staff_benefit_variant_gcic.dateRefreshFrequency',
//        ]);
//
//        $this->leftJoin('staff_benefit_variant_dental', 'staff_benefit_variant_dental.id = elements.id');
//        $this->leftJoin('staff_benefit_variant_gcic', 'staff_benefit_variant_gcic.id = elements.id');
        // ---- END TEST 1 ----




        // ---- TEST 2 ------
//        $subQueryDental = (new Query())
//            ->select([
//                'staff_benefit_variant_dental.name',
//                'staff_benefit_variant_dental.trsId',
//                'staff_benefit_variant_dental.policyId',
//            ])
//            ->from('staff_benefit_variant_dental')
//            ->where('elements.id=staff_benefit_variant_dental.id');
//
//        $subQueryGcic = (new Query())
//            ->select([
//                'staff_benefit_variant_gcic.name',
//                'staff_benefit_variant_gcic.trsId',
//                'staff_benefit_variant_gcic.policyId',
//            ])
//            ->from('staff_benefit_variant_gcic')
//            ->where('elements.id=staff_benefit_variant_gcic.id');
//
//        $this->query
//            ->where(['exists',$subQueryDental])
//            ->orWhere(['exists',$subQueryGcic]);
        // ---- END TEST 2 ------





        // ---- TEST 3 ------
//        $this->query->select([
//            //dental
//            'staff_benefit_variant_dental.name',
//            'staff_benefit_variant_dental.trsId',
//            'staff_benefit_variant_dental.policyId',
//            //gcic
//            'staff_benefit_variant_gcic.name',
//            'staff_benefit_variant_gcic.trsId',
//            'staff_benefit_variant_gcic.policyId',
//            'staff_benefit_variant_gcic.rateReviewGuaranteeDate',
//            'staff_benefit_variant_gcic.costingBasis',
//            'staff_benefit_variant_gcic.unitRate',
//            'staff_benefit_variant_gcic.unitRateSuffix',
//            'staff_benefit_variant_gcic.freeCoverLevelAutomaticAcceptanceLimit',
//            'staff_benefit_variant_gcic.dateRefreshFrequency',
//        ]);
        // ---- END TEST 3 ------



        if ($this->policyId) {
            $this->subQuery->andWhere(Db::parseParam('staff_benefit_variant.policyId', $this->policyId));
        }

        return parent::beforePrepare();
    }
}
