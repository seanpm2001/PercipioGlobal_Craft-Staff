<?php

namespace percipiolondon\staff\gql\queries;

use craft\gql\base\Query;

use GraphQL\Type\Definition\Type;

use percipiolondon\staff\gql\arguments\elements\Employer as EmployerArguments;
use percipiolondon\staff\gql\interfaces\elements\Employer as EmployerInterface;
use percipiolondon\staff\gql\resolvers\elements\Employer as EmployerResolver;
use percipiolondon\staff\helpers\Gql as GqlHelper;

class Employer extends Query
{
    // Public Methods
    // =========================================================================

    public static function getQueries($checkToken = true): array
    {
        if ($checkToken && !GqlHelper::canQueryEmployers()) {
            return [];
        }

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
            ],
        ];
    }
}
