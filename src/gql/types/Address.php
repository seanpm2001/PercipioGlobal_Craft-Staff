<?php

namespace percipiolondon\staff\gql\types;

use percipiolondon\staff\gql\base\GqlTypeTrait;
use GraphQL\Type\Definition\Type;

/**
 * Class Address
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class Address
{
    use GqlTypeTrait;

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'Address';
    }

    /**
     * List of fields for this type.
     *
     * @return array
     */
    public static function getFieldDefinitions(): array
    {
        return [
            'line1' => [
                'name' => 'line1',
                'type' => Type::string(),
                'description' => 'Line 1 of the address type',
            ],
            'line2' => [
                'name' => 'line2',
                'type' => Type::string(),
                'description' => 'Line 2 of the address type',
            ],
            'line3' => [
                'name' => 'line3',
                'type' => Type::string(),
                'description' => 'Line 3 of the address type',
            ],
            'line4' => [
                'name' => 'line4',
                'type' => Type::string(),
                'description' => 'Line 4 of the address type',
            ],
            'postCode' => [
                'name' => 'postCode',
                'type' => Type::string(),
                'description' => 'Post code of the address type',
            ],
            'country' => [
                'name' => 'country',
                'type' => Type::string(),
                'description' => 'Country of the address type',
            ],
        ];
    }

}