<?php

namespace percipiolondon\staff\gql\types;

use craft\gql\base\GqlTypeTrait;
use craft\gql\types\DateTime;
use GraphQL\Type\Definition\Type;

use percipiolondon\staff\gql\types\OverseasEmployerDetails;
use percipiolondon\staff\gql\types\PensionerPayroll;

/**
 * Class StarterDetails
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class StarterDetails
{
    use GqlTypeTrait;

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'starterDetails';
    }

    /**
     * List of fields for this type.
     *
     * @return array
     */
    public static function getFieldDefinitions(): array
    {
        return [
            'startDate' => [
                'name' => 'startDate',
                'type' => DateTime::getType(),
                'description' => 'Start date of employment.',
            ],
            // TODO Create Enum
            'starterDeclaration' => [
                'name' => 'starterDeclaration',
                'type' => Type::string(),
                'description' => 'Starter declaration.',
            ],
            'overseasEmployerDetails' => [
                'name' => 'overseasEmployerDetails',
                'type' => OverseasEmployerDetails::getType(),
            ],
            'pensionerPayroll' => [
                'name' => 'pensionerPayroll',
                'type' => PensionerPayroll::getType(),
            ],
        ];
    }
}
