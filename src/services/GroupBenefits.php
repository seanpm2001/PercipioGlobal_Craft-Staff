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
use percipiolondon\staff\records\BenefitTypeDental;
use percipiolondon\staff\records\BenefitTypeGroupCriticalIllnessCover;

class GroupBenefits extends Component
{
    // Public Methods
    // =========================================================================
    public function getBenefitTypeData(int $id, ?string $type): ?array
    {
        $query = match ($type) {
            'dental' => new BenefitTypeDental(),
            'group-critical-illness-cover' => new BenefitTypeGroupCriticalIllnessCover(),
            'group-income-protection', 'group-death-in-service', 'group-life-assurance', 'health-cash-plan', 'private-medical-insurance' => null,
            default => null,
        };

        if(is_null($query)) {
            return null;
        }

        return $query::findOne($id)?->toArray();
    }
}