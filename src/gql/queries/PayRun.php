<?php

namespace percipiolondon\staff\gql\queries;

use craft\gql\base\Query;

use GraphQL\Type\Definition\Type;

use percipiolondon\staff\helpers\Gql as GqlHelper;
use percipiolondon\staff\gql\arguments\elements\PayRun as PayRunArguments;
use percipiolondon\staff\gql\interfaces\elements\PayRun as PayRunInterface;
use percipiolondon\staff\gql\resolvers\elements\PayRun as PayRunResolver;

class PayRun extends Query
{
    // Public Methods
    // =========================================================================

    public static function getQueries($checkToken = true): array
    {
        return [
            'payruns' => [
                'type' => Type::listOf(PayRunInterface::getType()),
                'args' => PayRunArguments::getArguments(),
                'resolve' => PayRunResolver::class . '::resolve',
                'description' => 'This query is used to query for all the payruns.',
                'complexity' => GqlHelper::relatedArgumentComplexity(),
            ],
            'payrun' => [
                'type' => PayRunInterface::getType(),
                'args' => PayRunArguments::getArguments(),
                'resolve' => PayRunResolver::class . '::resolveOne',
                'description' => 'This query is used to query for a single payrun.',
                'complexity' => GqlHelper::relatedArgumentComplexity(),
            ]
        ];
    }

}