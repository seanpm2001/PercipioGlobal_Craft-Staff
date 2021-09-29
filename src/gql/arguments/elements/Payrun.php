<?php

namespace percipiolondon\craftstaff\gql\arguments\elements;

use Craft;
use craft\gql\base\ElementArguments;
use craft\gql\types\QueryArgument;
use GraphQL\Type\Definition\Type;

class PayRun extends ElementArguments
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
                'description' => 'Narrows the query results based on the payrun’ employers.',
            ],
            'staffologyId' => [
                'name' => 'staffologyId',
                'type' => Type::listOf(Type::int()),
                'description' => 'Narrows the query results based on the payrun’ staffology ID.',
            ],
        ]);
    }
}