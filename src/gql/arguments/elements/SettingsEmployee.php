<?php

namespace percipiolondon\staff\gql\arguments\elements;

use GraphQL\Type\Definition\Type;
use percipiolondon\staff\gql\base\HardingArguments;

class SettingsEmployee extends HardingArguments
{
    public static function getArguments(): array
    {
        return array_merge(parent::getArguments(), [
            'settingsId' => [
                'name' => 'employerId',
                'type' => Type::listOf(Type::int()),
            ],
            'employeeId' => [
                'name' => 'employeeId',
                'type' => Type::int(),
            ],
        ]);
    }
}