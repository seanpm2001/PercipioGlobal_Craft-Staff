<?php

namespace percipiolondon\staff\gql\queries;

use craft\gql\base\Query;
use GraphQL\Type\Definition\Type;
use percipiolondon\staff\gql\arguments\elements\SettingsEmployee as SettingsEmployeeArguments;
use percipiolondon\staff\gql\interfaces\elements\SettingsEmployee as SettingsEmployeeInterface;
use percipiolondon\staff\gql\resolvers\elements\SettingsEmployee as SettingsEmployeeResolver;
use percipiolondon\staff\helpers\Gql as GqlHelper;

class SettingsEmployee extends Query
{
    public static function getQueries($checkToken = true): array
    {
        if($checkToken && !GqlHelper::canQuerySettingsEmployee()) {
            return [];
        }

        return [
            'SettingsEmployees' => [
                'type' => Type::listOf(SettingsEmployeeInterface::getType()),
                'args' => SettingsEmployeeArguments::getArguments(),
                'resolve' => SettingsEmployeeResolver::class . '::resolve',
                'description' => 'This query is used to query for all the employee settings',
                'complexity' => GqlHelper::relatedArgumentComplexity()
            ],
//            'SettingsEmployee' => [
//                'type' => SettingsEmployeeInterface::getType(),
//                'args' => SettingsEmployeeArguments::getArguments(),
//                'resolve' => SettingsEmployeeResolver::class . '::resolveOne',
//                'description' => 'This query is used to query for all the Requests',
//                'complexity' => GqlHelper::relatedArgumentComplexity()
//            ],
        ];
    }
}