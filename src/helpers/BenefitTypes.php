<?php

namespace percipiolondon\staff\helpers;

use craft\db\ActiveRecord;
use craft\helpers\Db;
use percipiolondon\staff\records\BenefitTypeDental;
use percipiolondon\staff\records\BenefitTypeGroupCriticalIllnessCover;
use percipiolondon\staff\records\BenefitTypeGroupDeathInService;

class BenefitTypes
{
    public array $benefitTypes = [
        'dental' => 'Dental',
        'group-critical-illness-cover' => 'Group Critical Illness Cover',
        'group-death-in-service' => 'Group Death In Service',
        'group-income-protection' => 'Group Income Protection',
        'group-life-assurance' => 'Life Assurance',
        'health-cash-plan' => 'Health Cash Plan',
        'private-medical-insurance' => 'Private Medical Insurance',
    ];

    public array $benefitTypesTables = [
        'dental' => 'staff_benefit_type_dental',
        'group-critical-illness-cover' => 'staff_benefit_type_group_critical_illness_cover',
        'group-death-in-service' => 'staff_benefit_type_group_death_in_service',
        'group-income-protection' => 'staff_benefit_type_group_income_protection',
        'group-life-assurance' => 'staff_benefit_type_group_life_assurance',
        'health-cash-plan' => 'staff_benefit_type_health_cash_plan',
        'private-medical-insurance' => 'staff_benefit_type_private_medical_insurance',
    ];

    public array $benefitTypesArrays = [
        'dental' => 'arrBenefitTypeDental',
        'group-critical-illness-cover' => 'arrBenefitTypeGroupCriticalIllnessCover',
        'group-death-in-service' => 'arrBenefitTypeGroupDeathInService',
        'group-income-protection' => 'arrBenefitTypeGroupIncomeProtection',
        'group-life-assurance' => 'arrBenefitTypeGroupLifeAssurance',
        'health-cash-plan' => 'arrBenefitTypeCashPlan',
        'private-medical-insurance' => 'arrBenefitTypePrivateMedicalInsurance',
    ];

    public function setGroupCriticalIllnessCover(array $fields, bool $save = true): bool|array
    {

        $type = BenefitTypeGroupCriticalIllnessCover::findOne($fields['id'] ?? null);

        if(!$type){
            $type = new BenefitTypeGroupCriticalIllnessCover();
        }

        //generic
        $type = $this->_prepareGenericFields($fields, $type);

        //specific
        $type->rateReviewGuaranteeDate = Db::prepareDateForDb($fields['rateReviewGuaranteeDate'] ?? null);
        $type->costingBasis = $fields['costingBasis'] ?? null;
        $type->unitRate = $fields['unitRate'] ?? null;
        $type->unitRateSuffix = $fields['unitRateSuffix'] ?? null;
        $type->freeCoverLevelAutomaticAcceptanceLimit = $fields['freeCoverLevelAutomaticAcceptanceLimit'] ?? null;
        $type->dateRefreshFrequency = $fields['dateRefreshFrequency'] ?? null;

        if($save) {
            return $type->save();
        }

        return $type->toArray();
    }

    public function setGroupDeathInService(array $fields, bool $save = true): bool|array
    {

        $type = BenefitTypeGroupDeathInService::findOne($fields['id'] ?? null);

        if(!$type){
            $type = new BenefitTypeGroupDeathInService();
        }

        //generic
        $type = $this->_prepareGenericFields($fields, $type);

        //specific
        $type->rateReviewGuaranteeDate = Db::prepareDateForDb($fields['rateReviewGuaranteeDate'] ?? null);
        $type->costingBasis = $fields['costingBasis'] ?? null;
        $type->unitRate = $fields['unitRate'] ?? null;
        $type->unitRateSuffix = $fields['unitRateSuffix'] ?? null;
        $type->freeCoverLevelAutomaticAcceptanceLimit = $fields['freeCoverLevelAutomaticAcceptanceLimit'] ?? null;
        $type->dateRefreshFrequency = $fields['dateRefreshFrequency'] ?? null;
        $type->pensionSchemeTaxReferenceNumber = $fields['pensionSchemeTaxReferenceNumber'] ?? null;
        $type->dateOfTrustDeed = Db::prepareDateForDb($fields['dateOfTrustDeed'] ?? null);
        $type->eventLimit = $fields['eventLimit'] ?? null;

        if($save) {
            return $type->save();
        }

        return $type->toArray();
    }

    private function _prepareGenericFields(array $fields, ActiveRecord $type): ActiveRecord
    {
        $type->id = $fields['id'] ?? null;
        $type->providerId = $fields['providerId'] ?? null;
        $type->internalCode = $fields['internalCode'] ?? null;
        $type->status = $fields['status'] ?? null;
        $type->policyName = $fields['policyName'] ?? null;
        $type->policyNumber = $fields['policyNumber'] ?? null;
        $type->policyHolder = $fields['policyHolder'] ?? null;
        $type->content = $fields['content'] ?? null;
        $type->policyStartDate = Db::prepareDateForDb($fields['policyStartDate'] ?? null);
        $type->policyRenewalDate = Db::prepareDateForDb($fields['policyRenewalDate'] ?? null);
        $type->paymentFrequency = $fields['paymentFrequency'] ?? null;
        $type->commissionRate = $fields['commissionRate'] ?? null;

        return $type;
    }
}
