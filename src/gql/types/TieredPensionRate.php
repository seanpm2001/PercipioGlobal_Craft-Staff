<?php

namespace percipiolondon\staff\gql\types;

use GraphQL\Type\Definition\Type;

use craft\gql\base\GqlTypeTrait;


/**
 * Class TieredPensionRate
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class TieredPensionRate
{
    use GqlTypeTrait;

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'tieredPensionRate';
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
            ],
            'description' => [
                'name' => 'description',
                'type' => Type::string(),
            ],
            'rangeStart' => [
                'name' => 'rangeStart',
                'type' => Type::float(),
            ],
            'rate' => [
                'name' => 'rate',
                'type' => Type::float(),
            ],
        ];
    }

}
