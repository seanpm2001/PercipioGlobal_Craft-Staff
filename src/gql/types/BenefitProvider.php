<?php

namespace percipiolondon\staff\gql\types;

use craft\gql\base\GqlTypeTrait;
use GraphQL\Type\Definition\Type;

/**
 * Class BankDetails
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class BenefitProvider
{
    use GqlTypeTrait;

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'benefitProvider';
    }

    /**
     * List of fields for this type.
     *
     * @return array
     */
    public static function getFieldDefinitions(): array
    {
        return [
            'name' => [
                'name' => 'name',
                'type' => Type::string(),
                'description' => 'The benefit provider.',
            ],
            'url' => [
                'name' => 'url',
                'type' => Type::string(),
                'description' => 'The benefit provider website.'
            ],
            'logo' => [
                'name' => 'logo',
                'type' => Type::string(),
                'description' => 'The url of the logo asset.'
            ],
            'content' => [
                'name' => 'content',
                'type' => Type::string(),
                'description' => 'The provider description.'
            ],
        ];
    }
}
