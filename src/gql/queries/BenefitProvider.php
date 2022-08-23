<?php

namespace percipiolondon\staff\gql\queries;

use craft\gql\base\Query;

use GraphQL\Type\Definition\Type;

use percipiolondon\staff\gql\arguments\elements\BenefitProvider as BenefitProviderArguments;
use percipiolondon\staff\gql\interfaces\elements\BenefitProvider as BenefitProviderInterface;
use percipiolondon\staff\gql\resolvers\elements\BenefitProvider as BenefitProviderResolver;
use percipiolondon\staff\helpers\Gql as GqlHelper;

class BenefitProvider extends Query
{
    public static function getQueries($checkToken = true): array
    {
        if ($checkToken && !GqlHelper::canQueryBenefitProviders()) {
            return [];
        }

        return [
            'BenefitProviders' => [
                'type' => Type::listOf(BenefitProviderInterface::getType()),
                'args' => BenefitProviderArguments::getArguments(),
                'resolve' => BenefitProviderResolver::class . '::resolve',
                'description' => 'This query is used to query for all the benefit providers.',
                'complexity' => GqlHelper::relatedArgumentComplexity(),
            ],
            'BenefitProvider' => [
                'type' => BenefitProviderInterface::getType(),
                'args' => BenefitProviderArguments::getArguments(),
                'resolve' => BenefitProviderResolver::class . '::resolveOne',
                'description' => 'This query is used to query for a single benefit provider.',
                'complexity' => GqlHelper::relatedArgumentComplexity(),
            ],
        ];
    }
}
