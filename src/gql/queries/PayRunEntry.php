<?php

namespace percipiolondon\staff\gql\queries;

use craft\gql\base\Query;

use GraphQL\Type\Definition\Type;

use percipiolondon\staff\helpers\Gql as GqlHelper;
use percipiolondon\staff\gql\arguments\elements\PayRunEntry as PayRunEntryArguments;
use percipiolondon\staff\gql\interfaces\elements\PayRunEntry as PayRunEntryInterface;
use percipiolondon\staff\gql\resolvers\elements\PayRunEntry as PayRunEntryResolver;

class PayRunEntry extends Query
{

    public static function getQueries($checkToken = true): array
    {
        if ($checkToken && !GqlHelper::canQueryPayrunEntries()) {
            return [];
        }

        return [
            'payrunentries' => [
                'type' => Type::listOf(PayRunEntryInterface::getType()),
                'args' => PayRunEntryArguments::getArguments(),
                'resolve' => PayRunEntryResolver::class . '::resolve',
                'description' => 'This query is used to query for all the Payrun Entries.',
                'complexity' => GqlHelper::relatedArgumentComplexity(),
            ],
            'payrunentry' => [
                'type' => PayRunEntryInterface::getType(),
                'args' => PayRunEntryArguments::getArguments(),
                'resolve' => PayRunEntryResolver::class . '::resolveOne',
                'description' => 'This query is used to query for a single Payrun Entry.',
                'complexity' => GqlHelper::relatedArgumentComplexity(),
            ]
        ];
    }

}