<?php

namespace percipiolondon\staff\gql\arguments\elements;

use GraphQL\Type\Definition\Type;

use percipiolondon\staff\gql\base\HardingArguments;

class PayRunEntry extends HardingArguments
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
                'description' => 'Narrows the query results based on the payruns’ employer ID.',
            ],
            'employeeId' => [
                'name' => 'employeeId',
                'type' => Type::listOf(Type::int()),
                'description' => 'Narrows the query results based on the payruns’ employee ID.',
            ],
            'payRunId' => [
                'name' => 'payRunId',
                'type' => Type::listOf(Type::int()),
                'description' => 'Narrows the query results based on the payruns’ pay run ID.',
            ],
            'staffologyId' => [
                'name' => 'staffologyId',
                'type' => Type::listOf(Type::string()),
                'description' => 'Narrows the query results based on the payruns’ staffology ID.',
            ],
            'isClosed' => [
                'name' => 'isClosed',
                'type' => Type::boolean(),
                'description' => 'Narrows the query results based if the payruns is closed.',
            ],
        ]);
    }
}
