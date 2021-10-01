<?php

namespace percipiolondon\craftstaff\gql\arguments\elements;

use Craft;
use craft\gql\base\ElementArguments;
use craft\gql\types\QueryArgument;
use GraphQL\Type\Definition\Type;

class PayRunEntry extends ElementArguments
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
                'description' => 'Narrows the query results based on the payrun’ employer ID.',
            ],
            'employeeId' => [
                'name' => 'employeeId',
                'type' => Type::listOf(Type::int()),
                'description' => 'Narrows the query results based on the payrun’ employee ID.',
            ],
            'payRunId' => [
                'name' => 'payRunId',
                'type' => Type::listOf(Type::int()),
                'description' => 'Narrows the query results based on the payrun’ pay run ID.',
            ],
            'staffologyId' => [
                'name' => 'staffologyId',
                'type' => Type::listOf(Type::string()),
                'description' => 'Narrows the query results based on the payrun’ staffology ID.',
            ],
            'isClosed' => [
                'name' => 'isClosed',
                'type' => Type::boolean(),
                'description' => 'Narrows the query results based if the payrun is closed.',
            ],
        ]);
    }
}