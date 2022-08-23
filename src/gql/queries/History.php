<?php

namespace percipiolondon\staff\gql\queries;

use craft\gql\base\Query;
use GraphQL\Type\Definition\Type;
use percipiolondon\staff\gql\arguments\elements\History as HistoryArguments;
use percipiolondon\staff\gql\interfaces\elements\History as HistoryInterface;
use percipiolondon\staff\gql\resolvers\elements\History as HistoryResolver;
use percipiolondon\staff\helpers\Gql as GqlHelper;

class History extends Query
{
    public static function getQueries($checkToken = true): array
    {
        if($checkToken && !GqlHelper::canQueryHistory()) {
            return [];
        }

        return [
            'Histories' => [
                'type' => Type::listOf(HistoryInterface::getType()),
                'args' => HistoryArguments::getArguments(),
                'resolve' => HistoryResolver::class . '::resolve',
                'description' => 'This query is used to query for all the history',
                'complexity' => GqlHelper::relatedArgumentComplexity()
            ],
            'History' => [
                'type' => HistoryInterface::getType(),
                'args' => HistoryArguments::getArguments(),
                'resolve' => HistoryResolver::class . '::resolveOne',
                'description' => 'This query is used to query for all the history',
                'complexity' => GqlHelper::relatedArgumentComplexity()
            ],
            'HistoriesCount' => [
                'type' => Type::nonNull(Type::int()),
                'args' => HistoryArguments::getArguments(),
                'resolve' => HistoryResolver::class . '::resolveCount',
                'description' => 'This query is used to return the number of history.',
                'complexity' => GqlHelper::singleQueryComplexity(),
            ],
        ];
    }
}