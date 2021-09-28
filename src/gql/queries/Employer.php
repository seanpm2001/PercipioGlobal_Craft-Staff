<?php

namespace percipiolondon\craftstaff\gql\queries;

use GraphQL\Type\Definition\Type;
use percipiolondon\craftstaff\helpers\Gql as GqlHelper;
use percipiolondon\craftstaff\gql\arguments\elements\Employer as EmployerArguments;
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
                'args' => EmployerArguments::getArguments(),
                'resolve' => EmployerResolver::class . '::resolve',
                'description' => 'This query is used to query for employers.',
                'complexity' => GqlHelper::relatedArgumentComplexity(),
            ],
            'employer' => [
                'type' => EmployerInterface::getType(),
                'args' => EmployerArguments::getArguments(),
                'resolve' => EmployerResolver::class . '::resolveOne',
                'description' => 'This query is used to query for a single employer.',
                'complexity' => GqlHelper::relatedArgumentComplexity(),
            ]
        ];
    }
}
