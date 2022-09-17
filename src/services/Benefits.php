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
use craft\base\Element;
use craft\helpers\DateTimeHelper;
use percipiolondon\staff\elements\BenefitVariant;
use percipiolondon\staff\records\BenefitPolicy;
use percipiolondon\staff\records\BenefitVariantGcic;
use percipiolondon\staff\records\BenefitVariantGdis;
use percipiolondon\staff\records\TotalRewardsStatement;
use yii\web\Request;

/**
 * Class Benefits
 *
 * @package percipiolondon\staff\services
 */
class Benefits extends Component
{
    // Public Methods
    // =========================================================================


    /* GETTERS */
    /**
     * @param int $id
     * @param string|null $type
     * @return array|null
     * @throws \Exception
     */
    public function getBenefitTypeData(int $id, ?string $type): ?array
    {
        $query = match ($type) {
            'dental' => new BenefitPolicy(),
            'group-critical-illness-cover' => new BenefitVariantGcic(),
            'group-death-in-service' => new BenefitVariantGdis(),
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