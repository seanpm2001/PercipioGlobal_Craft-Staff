<?php

namespace percipiolondon\craftstaff\gql\types;

use percipiolondon\craftstaff\gql\base\GqlTypeTrait;
use GraphQL\Type\Definition\Type;


/**
 * Class Department
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class Department
{
    use GqlTypeTrait;

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'department';
    }

    /**
     * List of fields for this type.
     *
     * @return array
     */
    public static function getFieldDefinitions(): array
    {
        return [
            'code' => [
                'name' => 'code',
                'type' => Type::string(),
                'description' => 'The unique code for this Department.',
            ],
            'title' => [
                'name' => 'title',
                'type' => Type::string(),
                'description' => 'The name of this Department.',
            ],
            'employeeCount' => [
                'name' => 'employeeCount',
                'type' => Type::int(),
                'description' => 'The number of employees with this set as their primary department.',
            ],
        ];
    }

}
