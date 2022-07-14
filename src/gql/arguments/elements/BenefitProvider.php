<?php

namespace percipiolondon\staff\gql\arguments\elements;

use GraphQL\Type\Definition\Type;

use percipiolondon\staff\gql\base\HardingArguments;

class BenefitProvider extends HardingArguments
{
    /**
     * @inheritdoc
     */
    public static function getArguments(): array
    {
        return array_merge(parent::getArguments(), [
            'name' => [
                'name' => 'name',
                'type' => Type::listOf(Type::string()),
                'description' => 'Narrows the query results based on the benefit providers’ name.',
            ],
        ]);
    }
}
