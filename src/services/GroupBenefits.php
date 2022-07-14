<?php
/**
 * staff-management plugin for Craft CMS 3.x
 *
 * Craft Staff Management provides an HR solution for payroll and benefits
 *
 * @link      http://percipio.london
 * @copyright Copyright (c) 2021 Percipio
 */

namespace percipiolondon\staff\services;

use craft\base\Component;
use craft\helpers\DateTimeHelper;
use percipiolondon\staff\records\BenefitTypeDental;
use percipiolondon\staff\records\BenefitTypeGroupCriticalIllnessCover;
use percipiolondon\staff\records\BenefitTypeGroupDeathInService;

class GroupBenefits extends Component
{
    // Public Methods
    // =========================================================================
    public function getBenefitTypeData(int $id, ?string $type): ?array
    {
        $query = match ($type) {
            'dental' => new BenefitTypeDental(),
            'group-critical-illness-cover' => new BenefitTypeGroupCriticalIllnessCover(),
            'group-death-in-service' => new BenefitTypeGroupDeathInService(),
            'group-income-protection', 'group-life-assurance', 'health-cash-plan', 'private-medical-insurance' => null,
            default => null,
        };

        if(is_null($query)) {
            return null;
        }

        // convert to array notation
        $query = $query::findOne($id)?->toArray();

        // convert generic field dates to the correct timezone
        $query['policyStartDate'] = DateTimeHelper::toDateTime($query['policyStartDate']);
        $query['policyRenewalDate'] = DateTimeHelper::toDateTime($query['policyRenewalDate']);

        // convert custom benefit type dates to the correct timezone
        switch($type) {
            case 'group-critical-illness-cover':
                $query['rateReviewGuaranteeDate'] = DateTimeHelper::toDateTime($query['rateReviewGuaranteeDate']);
                break;
            case 'group-income-protection':
                $query['rateReviewGuaranteeDate'] = DateTimeHelper::toDateTime($query['rateReviewGuaranteeDate']);
                $query['dateOfTrustDeed'] = DateTimeHelper::toDateTime($query['dateOfTrustDeed']);
                break;
        }

        return $query;
    }
}