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
                'type' => Type::listOf(Type::string()),
                'description' => 'Narrows the query results based on the employees’ employers.',
            ],
            'userId' => [
                'name' => 'userId',
                'type' => Type::listOf(Type::string()),
                'description' => 'Narrows the query results based on the employees’ user ID.',
            ],
        ]);
    }
}