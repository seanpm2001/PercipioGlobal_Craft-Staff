<?php

namespace percipiolondon\staff\gql\arguments\elements;

use GraphQL\Type\Definition\Type;

use percipiolondon\staff\gql\base\HardingArguments;

class Notification extends HardingArguments
{
    /**
     * @inheritdoc
     */
    public static function getArguments(): array
    {
        return array_merge(parent::getArguments(), [
            'employeeId' => [
                'name' => 'employeeId',
                'type' => Type::int(),
            ],
            'type' => [
                'name' => 'type',
                'type' => Type::string(),
            ],
            'viewed' => [
                'name' => 'viewed',
                'type' => Type::boolean()
            ]
        ]);
    }
}
