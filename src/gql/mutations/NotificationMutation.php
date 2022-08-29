<?php

namespace percipiolondon\staff\gql\mutations;

use percipiolondon\staff\gql\interfaces\elements\Notification;
use Craft;
use percipiolondon\staff\helpers\Gql as GqlHelper;
use craft\gql\base\Mutation;
use GraphQL\Type\Definition\Type;
use percipiolondon\staff\gql\resolvers\mutations\Notification as NotificationMutationResolver;

class NotificationMutation extends Mutation
{
    public static function getMutations($checkToken = true): array
    {
        if ($checkToken && !GqlHelper::canMutateNotifications()) {
            return [];
        }

        $resolver = Craft::createObject(NotificationMutationResolver::class);

        $mutations = [];

        $mutations['UpdateNotificationViewed'] = [
            'name' => 'UpdateNotificationViewed',
            'args' => [
                'id' => Type::nonNull(Type::listOf(Type::int())),
                'viewed' => Type::nonNull(Type::boolean())
            ],
            'resolve' => [$resolver, 'updateNotificationViewed'],
            'description' => 'Updates the notification viewed',
            'type' => Type::listOf(Notification::getType())
        ];

        return $mutations;
    }
}