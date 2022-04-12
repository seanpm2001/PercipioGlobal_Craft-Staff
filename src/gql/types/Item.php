<?php

namespace percipiolondon\staff\gql\types;

use GraphQL\Type\Definition\Type;
use percipiolondon\staff\gql\base\GqlTypeTrait;

/**
 * Class Item
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class Item
{
    use GqlTypeTrait;

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'item';
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
            'name' => [
                'name' => 'name',
                'type' => Type::string(),
            ],
            'metadata' => [
                'name' => 'metadata',
                'type' => Type::object(),
            ],
            'url' => [
                'name' => 'url',
                'type' => Type::string(),
            ],
        ];
    }
}
