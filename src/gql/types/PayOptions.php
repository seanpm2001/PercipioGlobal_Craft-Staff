<?php

namespace percipiolondon\staff\gql\types;

use GraphQL\Type\Definition\Type;
use percipiolondon\staff\gql\base\GqlTypeTrait;

/**
 * Class Address
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class PayOptions
{
    use GqlTypeTrait;

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'payOptions';
    }

    /**
     * List of fields for this type.
     *
     * @return array
     */
    public static function getFieldDefinitions(): array
    {
        return [
            'period' => [
                'name' => 'period',
                'type' => Type::string(),
                'description' => 'Period.',
            ],
            'ordinal' => [
                'name' => 'ordinal',
                'type' => Type::int(),
                'description' => 'Ordinal.',
            ],
            'payAmount' => [
                'name' => 'payAmount',
                'type' => Type::int(),
                'description' => 'Amount to pay.',
            ],
            'basis' => [
                'name' => 'basis',
                'type' => Type::String(),
                'description' => 'Basis.',
            ],
            'nationalMinimumWage' => [
                'name' => 'nationalMinimumWage',
                'type' => Type::boolean(),
                'description' => 'If national minimum wage applies.',
            ],
            'payAmountMultiplier' => [
                'name' => 'payAmountMultiplier',
                'type' => Type::int(),
                'description' => 'Pay amount multiplier.',
            ],
            'baseHourlyRate' => [
                'name' => 'baseHourlyRate',
                'type' => Type::float(),
                'description' => 'Base Hourly Rate.',
            ],
            'autoAdjustForLeave' => [
                'name' => 'autoAdjustForLeave',
                'type' => Type::boolean(),
                'description' => 'Auto adjustments for leave.',
            ],
            'method' => [
                'name' => 'method',
                'type' => Type::string(),
                'description' => 'Payment method.',
            ],
            'withholdTaxRefundIfPayIsZero' => [
                'name' => 'withholdTaxRefundIfPayIsZero',
                'type' => Type::boolean(),
                'description' => 'Withhold the tax refund if payment is zero.',
            ],
            'fpsFields' => [
                'name' => 'fpsFields',
                'type' => FpsFields::getType(),
                'description' => 'FPS Fields.',
            ],
            'taxAndNi' => [
                'name' => 'taxAndNi',
                'type' => TaxAndNi::getType(),
                'description' => 'Tax and National Insurance',
            ],
        ];
    }
}
