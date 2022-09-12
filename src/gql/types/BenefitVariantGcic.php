<?php

namespace percipiolondon\staff\gql\types;

use craft\gql\base\GqlTypeTrait;
use GraphQL\Type\Definition\Type;

/**
 * Class Item
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class BenefitVariantGcic
{
    use GqlTypeTrait;

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'benefitVariantGcic';
    }

    /**
     * List of fields for this type.
     *
     * @return array
     */
    public static function getFieldDefinitions(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::string(),
            ],
            'rateReviewGuaranteeDate' => [
                'name' => 'rateReviewGuaranteeDate',
                'type' => Type::string(),
            ],
            'costingBasis' => [
                'name' => 'costingBasis',
                'type' => Type::string(),
            ],
            'unitRate' => [
                'name' => 'unitRate',
                'type' => Type::float(),
            ],
            'unitRateSuffix' => [
                'name' => 'unitRateSuffix',
                'type' => Type::string(),
            ],
            'freeCoverLevelAutomaticAcceptanceLimit' => [
                'name' => 'freeCoverLevelAutomaticAcceptanceLimit',
                'type' => Type::float(),
            ],
            'dateRefreshFrequency' => [
                'name' => 'dateRefreshFrequency',
                'type' => Type::string(),
            ],
        ];
    }
}
