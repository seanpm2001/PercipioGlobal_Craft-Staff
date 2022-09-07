<?php

namespace percipiolondon\staff\gql\mutations;

use Craft;
use percipiolondon\staff\gql\types\Employee;
use percipiolondon\staff\helpers\Gql as GqlHelper;
use craft\gql\base\Mutation;
use percipiolondon\staff\gql\resolvers\mutations\BenefitVariantEmployee as Resolver;
use GraphQL\Type\Definition\Type;

class BenefitVariantEmployeesMutation extends Mutation
{
    public static function getMutations($checkToken = true): array
    {
        if ($checkToken && !GqlHelper::canMutateBenefitEmployees()) {
            return [];
        }

        $resolver = Craft::createObject(Resolver::class);

        $mutations = [];

        // Create a new benefit variant employee
        $mutations['AddEmployee'] = [
            'name' => 'AddEmployee',
            'args' => [
                'employeeId' => Type::nonNull(Type::int()),
                'variantId' => Type::nonNull(Type::int()),
            ],
            'resolve' => [$resolver, 'addEmployee'],
            'description' => 'Add a new employee to a variant',
            'type' => Employee::getType()
        ];

        // Delete a benefit variant employee
        $mutations['RemoveEmployee'] = [
            'name' => 'RemoveEmployee',
            'args' => [
                'employeeId' => Type::nonNull(Type::int()),
                'variantId' => Type::nonNull(Type::int()),
            ],
            'resolve' => [$resolver, 'removeEmployee'],
            'description' => 'Remove an employee to a variant',
            'type' => Employee::getType()
        ];

        return $mutations;
    }
}