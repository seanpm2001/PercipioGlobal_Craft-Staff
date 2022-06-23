<?php

namespace percipiolondon\staff\helpers;

use percipiolondon\staff\records\BenefitTypeDental;
use percipiolondon\staff\records\BenefitTypeGroupCriticalIllnessCover;

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

    public static function saveGroupCriticalIllnessCover(array $fields): bool {
        $type = new BenefitTypeGroupCriticalIllnessCover();

        //generic
        $type->providerId = $fields['providerId'] ?? null;
        $type->internalCode = $fields['internalCode'] ?? null;
        $type->status = $fields['status'] ?? null;
        $type->policyName = $fields['policyName'] ?? null;
        $type->policyNumber = $fields['policyNumber'] ?? null;
        $type->policyHolder = $fields['policyHolder'] ?? null;
        $type->content = $fields['content'] ?? null;
        $type->policyStartDate = $fields['policyStartDate'] ?? null;
        $type->policyRenewalDate = $fields['policyRenewalDate'] ?? null;
        $type->paymentFrequency = $fields['paymentFrequency'] ?? null;
        $type->commissionRate = $fields['commissionRate'] ?? null;

        \Craft::dd($type->validate());

        //specific
//        $type->rateReviewGuaranteeDate = $fields['rateReviewGuaranteeDate'];
//        $type->costingBasis = $fields['costingBasis'];

        $type->save();
    }
}
