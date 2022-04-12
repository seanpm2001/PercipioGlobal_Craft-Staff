<?php

namespace percipiolondon\staff\gql\types;

use craft\base\gql\GqlTypeTrait;
use GraphQL\Type\Definition\Type;

/**
 * Class Address
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class FpsFields
{
    use GqlTypeTrait;

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'fpsFields';
    }

    /**
     * List of fields for this type.
     *
     * @return array
     */
    public static function getFieldDefinitions(): array
    {
        return [
            'offPayrollWorker' => [
                'name' => 'offPayrollWorker',
                'type' => Type::boolean(),
                'description' => 'Off payroll worker?',
            ],
            'irregularPaymentPattern' => [
                'name' => 'irregularPaymentPattern',
                'type' => Type::boolean(),
                'description' => 'Irregular payment pattern?',
            ],
            'nonIndividual' => [
                'name' => 'nonIndividual',
                'type' => Type::boolean(),
                'description' => 'Non-individual?',
            ],
            'hoursNormallyWorked' => [
                'name' => 'hoursNormallyWorked',
                'type' => Type::String(),
                'description' => 'Hours normally worked.',
            ],
            'regularPayLines' => [
                'name' => 'regularPayLines',
                'type' => Type::ListOf(Type::string()),
                'description' => 'Regular pay lines.',
            ],
        ];
    }
}
