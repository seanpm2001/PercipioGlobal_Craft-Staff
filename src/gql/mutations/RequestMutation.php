<?php

namespace percipiolondon\staff\gql\mutations;

use Craft;
use percipiolondon\staff\gql\interfaces\elements\Request;
use percipiolondon\staff\helpers\Gql as GqlHelper;
use craft\gql\base\Mutation;
use percipiolondon\staff\gql\resolvers\mutations\Request as RequestMutationResolver;
use GraphQL\Type\Definition\Type;

class RequestMutation extends Mutation
{
    public static function getMutations(): array
    {
        // TODO: Implement getMutations() method.
        $resolver = Craft::createObject(RequestMutationResolver::class);

        $mutations = [];

        if(GqlHelper::canSchema('request','edit')) {
            // Create a new request
            $mutations['CreateRequest'] = [
                'name' => 'CreateRequest',
                'args' => [
                    'employerId' => Type::nonNull(Type::string())
                ],
                'resolve' => [$resolver, 'createRequest'],
                'description' => 'Saves a new request.',
                'type' => Request::getType()
            ];
        }

        return $mutations;
    }
}