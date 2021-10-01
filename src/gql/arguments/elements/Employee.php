<?php

namespace percipiolondon\craftstaff\gql\arguments\elements;

use Craft;
use craft\gql\base\ElementArguments;
use craft\gql\types\QueryArgument;
use GraphQL\Type\Definition\Type;

class Employee extends ElementArguments
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
                'description' => 'Narrows the query results based on the employees’ employers.',
            ],
            'userId' => [
                'name' => 'userId',
                'type' => Type::listOf(Type::int()),
                'description' => 'Narrows the query results based on the employees’ user ID.',
            ],
            'staffologyId' => [
                'name' => 'staffologyId',
                'type' => Type::listOf(Type::string()),
                'description' => 'Narrows the query results based on the staffology user ID.',
            ],
            'status' => [
                'name' => 'status',
                'type' => Type::listOf(Type::string()),
                'description' => 'Narrows the query results based on the employees` status.',
            ],
            'isDirector' => [
                'name' => 'isDirector',
                'type' => Type::boolean(),
                'description' => 'Narrows the query results based if the employee is a director.',
            ],
        ]);
    }
}