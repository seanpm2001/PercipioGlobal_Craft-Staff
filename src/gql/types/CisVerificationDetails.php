<?php

namespace percipiolondon\staff\gql\types;

use craft\gql\types\DateTime;

use GraphQL\Type\Definition\Type;
use percipiolondon\staff\gql\base\GqlTypeTrait;

/**
 * Class CisVerificationDetails
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class CisVerificationDetails
{
    use GqlTypeTrait;

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'cisVerificationDetails';
    }

    /**
     * List of fields for this type.
     *
     * @return array
     */
    public static function getFieldDefinitions(): array
    {
        return [
            // TODO CREATE ENUM
            'manuallyEntered' => [
                'name' => 'manuallyEntered',
                'type' => Type::boolean(),
            ],
            'matchInsteadOfVerify' => [
                'name' => 'matchInsteadOfVerify',
                'type' => Type::boolean(),
            ],
            'number' => [
                'name' => 'number',
                'type' => Type::string(),
            ],
            'date' => [
                'name' => 'date',
                'type' => DateTime::getType(),
            ],
            // TODO CREATE ENUM
            'taxStatus' => [
                'name' => 'taxStatus',
                'type' => Type::string(),
            ],
            'verificationRequest' => [
                'name' => 'verificationRequest',
                'type' => Type::string(),
            ],
        ];
    }
}
