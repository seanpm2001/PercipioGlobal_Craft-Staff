<?php

namespace percipiolondon\staff\gql\mutations;

use Craft;
use percipiolondon\staff\gql\interfaces\elements\SettingsEmployee;
use percipiolondon\staff\helpers\Gql as GqlHelper;
use craft\gql\base\Mutation;
use percipiolondon\staff\gql\resolvers\mutations\SettingsEmployee as SettingsEmployeeResolver;
use GraphQL\Type\Definition\Type;

class SettingsEmployeeMutation extends Mutation
{
    public static function getMutations($checkToken = true): array
    {
        if ($checkToken && !GqlHelper::canMutateSettingsEmployee()) {
            return [];
        }

        $resolver = Craft::createObject(SettingsEmployeeResolver::class);

        $mutations = [];

        // set settings for the employee
        $mutations['SetSettingsEmployee'] = [
            'name' => 'SetSettingsEmployee',
            'args' => [
                'employeeId' => Type::nonNull(Type::int()),
                'settings' => Type::nonNull(Type::listOf(Type::int())),
            ],
            'resolve' => [$resolver, 'setSettingsEmployee'],
            'description' => 'Saves a new request.',
            'type' => Type::listOf(SettingsEmployee::getType())
        ];

        return $mutations;
    }
}