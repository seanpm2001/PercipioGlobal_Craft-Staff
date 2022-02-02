<?php

namespace percipiolondon\staff\gql\queries;

use craft\gql\base\Query;
use GraphQL\Type\Definition\Type;
use percipiolondon\staff\gql\arguments\elements\Employee as EmployeeArguments;
use percipiolondon\staff\gql\interfaces\elements\Employee as EmployeeInterface;
use percipiolondon\staff\gql\resolvers\elements\Employee as EmployeeResolver;
use percipiolondon\staff\helpers\Gql as GqlHelper;

class Employee extends Query
{
    public static function getQueries($checkToken = true): array
    {
        return [
            'employees' => [
                'type' => Type::listOf(EmployeeInterface::getType()),
                'args' => EmployeeArguments::getArguments(),
                'resolve' => EmployeeResolver::class . '::resolve',
                'description' => 'This query is used to query for employees.',
            ],
            'employee' => [
                'type' => EmployeeInterface::getType(),
                'args' => EmployeeArguments::getArguments(),
                'resolve' => EmployeeResolver::class . '::resolveOne',
                'description' => 'This query is used to query for a single employee.',
            ]
        ];
    }
}
