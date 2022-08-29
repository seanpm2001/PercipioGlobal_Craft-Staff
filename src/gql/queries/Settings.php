<?php

namespace percipiolondon\staff\gql\queries;

use craft\gql\base\Query;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use percipiolondon\staff\helpers\Gql;
use percipiolondon\staff\gql\resolvers\elements\Settings as SettingsResolver;

class Settings extends Query
{
    public static function getQueries($checkToken = true): array
    {
        if($checkToken && !Gql::canQuerySettings()) {
            return [];
        }

        $queryType = new ObjectType([
            'name' => 'Settings',
            'fields' => [
                'id' => [
                    'type' => Type::string(),
                ],
                'name' => [
                    'type' => Type::string(),
                    'resolve' => function($rootValue) {
                        return \Craft::t('staff-management', $rootValue->name, 'en');
                    },
                ],
            ],
        ]);

        return [
            'Settings' => [
                'type' => Type::listOf(ObjectType::getNamedType($queryType)),
                'description' => 'This query is used to query all of the settings',
                'resolve' => SettingsResolver::class.'::resolve',
                'complexity' => Gql::relatedArgumentComplexity()
            ]
        ];
    }
}