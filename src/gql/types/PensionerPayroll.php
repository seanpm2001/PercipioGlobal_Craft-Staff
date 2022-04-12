<?php

namespace percipiolondon\staff\gql\types;

use craft\base\gql\GqlTypeTrait;
use GraphQL\Type\Definition\Type;

/**
 * Class PensionerPayroll
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class PensionerPayroll
{
    use GqlTypeTrait;

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'pensionerPayroll';
    }

    /**
     * List of fields for this type.
     *
     * @return array
     */
    public static function getFieldDefinitions(): array
    {
        return [
            'inReceiptOfPension' => [
                'name' => 'inReceiptOfPension',
                'type' => Type::boolean(),
                'description' => 'If set to true then the FPS will have the OccPenInd flag set to `yes`',
            ],
            'bereaved' => [
                'name' => 'bereaved',
                'type' => Type::boolean(),
                'description' => 'Indicator that Occupational Pension is being paid because they are a recently bereaved Spouse/Civil Partner.',
            ],
            'amount' => [
                'name' => 'amount',
                'type' => Type::float(),
                'description' => 'Annual amount of occupational pension.',
            ],
        ];
    }
}
