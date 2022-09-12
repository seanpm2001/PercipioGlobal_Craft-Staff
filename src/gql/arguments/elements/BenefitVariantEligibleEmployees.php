<?php

namespace percipiolondon\staff\gql\arguments\elements;

use GraphQL\Type\Definition\Type;

use percipiolondon\staff\gql\base\HardingArguments;

class BenefitVariantEligibleEmployees extends HardingArguments
{
    /**
     * @inheritdoc
     */
    public static function getArguments(): array
    {
        return array_merge(parent::getArguments(), [
            'policyId' => [
                'name' => 'policyId',
                'type' => Type::nonNull(Type::int()),
            ],
        ]);
    }
}
