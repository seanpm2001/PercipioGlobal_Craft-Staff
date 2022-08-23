<?php

namespace percipiolondon\staff\gql\mutations;

use Craft;
use craft\gql\types\DateTime;
use percipiolondon\staff\gql\interfaces\elements\Request;
use percipiolondon\staff\helpers\Gql as GqlHelper;
use craft\gql\base\Mutation;
use percipiolondon\staff\gql\resolvers\mutations\Request as RequestMutationResolver;
use GraphQL\Type\Definition\Type;

class RequestMutation extends Mutation
{
    public static function getMutations($checkToken = true): array
    {
        if ($checkToken && !GqlHelper::canMutateRequests()) {
            return [];
        }

        $resolver = Craft::createObject(RequestMutationResolver::class);

        $mutations = [];

        // Create a new request
        $mutations['CreateRequest'] = [
            'name' => 'CreateRequest',
            'args' => [
                'data' => Type::nonNull(Type::String()),
                'employerId' => Type::nonNull(Type::int()),
                'employeeId' => Type::nonNull(Type::int()),
                'type' => Type::nonNull(Type::String()),
                'status' => Type::String(),
            ],
            'resolve' => [$resolver, 'createRequest'],
            'description' => 'Saves a new request.',
            'type' => Request::getType()
        ];

        // Update a request
        $mutations['UpdateRequest'] = [
            'name' => 'UpdateRequest',
            'args' => [
                'id' => Type::int(),
                'administerId' => Type::int(),
                'dateAdministered' => DateTime::getType(),
                'note' => Type::String(),
                'status' => Type::String(),
            ],
            'resolve' => [$resolver, 'updateRequest'],
            'description' => 'Updates a request.',
            'type' => Request::getType()
        ];

        return $mutations;
    }
}