<?php

namespace percipiolondon\staff\gql\arguments\elements;

use GraphQL\Type\Definition\Type;

use percipiolondon\staff\gql\base\HardingArguments;

class History extends HardingArguments
{
    /**
     * @inheritdoc
     */
    public static function getArguments(): array
    {
        return array_merge(parent::getArguments(), [
            'employerId' => [
                'name' => 'employerId',
                'type' => Type::listOf(Type::int()),
            ],
            'employeeId' => [
                'name' => 'employeeId',
                'type' => Type::listOf(Type::int()),
            ],
            'type' => [
                'name' => 'type',
                'type' => Type::listOf(Type::string()),
            ],
        ]);
    }
}
