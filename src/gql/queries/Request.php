<?php

namespace percipiolondon\staff\gql\queries;

use craft\gql\base\Query;
use GraphQL\Type\Definition\Type;
use percipiolondon\staff\gql\arguments\elements\Request as RequestArguments;
use percipiolondon\staff\gql\interfaces\elements\Request as RequestInterface;
use percipiolondon\staff\gql\resolvers\elements\Request as RequestResolver;
use percipiolondon\staff\helpers\Gql as GqlHelper;

class Request extends Query
{
    public static function getQueries($checkToken = true): array
    {
        if($checkToken && !GqlHelper::canQueryRequests()) {
            return [];
        }

        return [
            'Requests' => [
                'type' => Type::listOf(RequestInterface::getType()),
                'args' => RequestArguments::getArguments(),
                'resolve' => RequestResolver::class . '::resolve',
                'description' => 'This query is used to query for all the Requests',
                'complexity' => GqlHelper::relatedArgumentComplexity()
            ],
            'Request' => [
                'type' => RequestInterface::getType(),
                'args' => RequestArguments::getArguments(),
                'resolve' => RequestResolver::class . '::resolveOne',
                'description' => 'This query is used to query for all the Requests',
                'complexity' => GqlHelper::relatedArgumentComplexity()
            ],
            'RequestCount' => [
                'type' => Type::nonNull(Type::int()),
                'args' => RequestArguments::getArguments(),
                'resolve' => RequestResolver::class . '::resolveCount',
                'description' => 'This query is used to return the number of entries.',
                'complexity' => GqlHelper::singleQueryComplexity(),
            ],
        ];
    }
}