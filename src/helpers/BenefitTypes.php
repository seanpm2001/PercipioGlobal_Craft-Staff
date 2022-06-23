<?php

namespace percipiolondon\staff\helpers;

use percipiolondon\staff\records\BenefitTypeDental;

class BenefitTypes
{
    public array $benefitTypes = [
        'dental',
        'group-critical-illness-cover',
        'group-death-in-service',
        'group-income-protection',
        'group-life-assurance',
        'health-cash-plan',
        'private-medical-insurance',
    ];

    public static function saveDental(array $fields): bool {
        $dental = new BenefitTypeDental();

        $dental->providerId = $fields['providerId'];

    }
}
