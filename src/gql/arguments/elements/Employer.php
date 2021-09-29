<?php

namespace percipiolondon\craftstaff\gql\arguments\elements;

use Craft;
use craft\gql\base\ElementArguments;
use craft\gql\types\QueryArgument;
use GraphQL\Type\Definition\Type;

class Employer extends ElementArguments
{
    /**
     * @inheritdoc
     */
    public static function getArguments(): array
    {
        return array_merge(parent::getArguments(), [
            'crn' => [
                'name' => 'crn',
                'type' => Type::listOf(Type::string()),
                'description' => 'Narrows the query results based on the employers’ company registration numbers.',
            ],
            'staffologyId' => [
                'name' => 'staffologyId',
                'type' => Type::listOf(Type::string()),
                'description' => 'Narrows the query results based on the staffology employer ID.',
            ],
        ]);
    }
}