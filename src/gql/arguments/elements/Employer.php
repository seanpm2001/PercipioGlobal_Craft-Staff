<?php

namespace percipiolondon\staff\gql\arguments\elements;

use GraphQL\Type\Definition\Type;

use percipiolondon\staff\gql\base\HardingArguments;

class Employer extends HardingArguments
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