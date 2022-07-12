<?php

namespace percipiolondon\staff\gql\arguments\elements;

use GraphQL\Type\Definition\Type;

use percipiolondon\staff\gql\base\HardingArguments;

class Request extends HardingArguments
{
    /**
     * @inheritdoc
     */
    public static function getArguments(): array
    {
        return array_merge(parent::getArguments(), [
            'employeeId' => [
                'name' => 'employeeId',
                'type' => Type::listOf(Type::string()),
            ],
            'status' => [
                'name' => 'status',
                'type' => Type::listOf(Type::string()),
            ],
        ]);
    }
}
