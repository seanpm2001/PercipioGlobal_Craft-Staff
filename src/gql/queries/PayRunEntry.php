<?php

namespace percipiolondon\craftstaff\gql\queries;

use craft\gql\base\Query;

use GraphQL\Type\Definition\Type;

use percipiolondon\craftstaff\gql\arguments\elements\PayRunEntry as PayRunEntryArguments;
use percipiolondon\craftstaff\gql\interfaces\elements\PayRunEntry as PayRunEntryInterface;
use percipiolondon\craftstaff\gql\resolvers\elements\PayRunEntry as PayRunEntryResolver;
use percipiolondon\craftstaff\helpers\Gql as GqlHelper;

class PayRunEntry extends Query
{

    public static function getQueries($checkToken = true): array
    {
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