<?php

namespace percipiolondon\staff\gql\types;

use craft\gql\base\GqlTypeTrait;
use GraphQL\Type\Definition\Type;

/**
 * Class Request
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class SettingsEmployee
{
    use GqlTypeTrait;

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'settingsEmployee';
    }

    /**
     * List of fields for this type.
     *
     * @return array
     */
    public static function getFieldDefinitions(): array
    {
        return [
            'settingsId' => [
                'name' => 'settingsId',
                'type' => Type::int(),
                'description' => 'Settings id',
            ],
            'employeeId' => [
                'name' => 'employeeId',
                'type' => Type::int(),
                'description' => 'Employee id',
            ],
        ];
    }
}
