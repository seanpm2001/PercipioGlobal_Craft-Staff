<?php

namespace percipiolondon\craftstaff\gql\queries;

use GraphQL\Type\Definition\Type;
use percipiolondon\craftstaff\helpers\Gql as GqlHelper;
use percipiolondon\craftstaff\gql\interfaces\elements\Employer as EmployerInterface;
use percipiolondon\craftstaff\gql\resolvers\elements\Employer as EmployerResolver;

class Employer extends \craft\gql\base\Query
{
    public static function getQueries($checkToken = true): array
    {
//        if($checkToken && !GqlHelper::canQueryEmployers()) {
//            return [];
//        }

        return [
            'employers' => [
                'type' => Type::listOf(EmployerInterface::getType()),
                'resolve' => EmployerResolver::class . '::resolve',
                'description' => 'This query is used to query for employers'
            ]
        ];
    }
}
