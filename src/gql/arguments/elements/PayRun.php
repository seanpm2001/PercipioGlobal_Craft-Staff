<?php

namespace percipiolondon\staff\gql\arguments\elements;

use GraphQL\Type\Definition\Type;

use percipiolondon\staff\gql\base\HardingArguments;

class PayRun extends HardingArguments
{
    /**
     * @inheritdoc
     */
    public static function getArguments(): array
    {
        return array_merge(parent::getArguments(), [
            'employerId' => [
                'name' => 'employerId',
                'type' => Type::listOf(Type::id()),
                'description' => 'Narrows the query results based on the payrun’ employers.',
            ],
            'staffologyId' => [
                'name' => 'staffologyId',
                'type' => Type::listOf(Type::id()),
                'description' => 'Narrows the query results based on the pay run’ staffology ID.',
            ],
            'isClosed' => [
                'name' => 'isClosed',
                'type' => Type::boolean(),
                'description' => 'Narrows the query results based if the pay run is closed.',
            ],
            'state' => [
                'name' => 'state',
                'type' => Type::listOf(Type::string()),
                'description' => 'Narrows the query results based on the pay run state.',
            ],
            'taxYear' => [
                'name' => 'taxYear',
                'type' => Type::listOf(Type::string()),
                'description' => 'Narrows the query results based on the pay run tax year.',
            ],
        ]);
    }
}
