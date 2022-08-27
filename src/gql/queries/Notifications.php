<?php

namespace percipiolondon\staff\gql\queries;

use craft\gql\base\Query;

use GraphQL\Type\Definition\Type;

use percipiolondon\staff\gql\arguments\elements\Notification as NotificationArguments;
use percipiolondon\staff\gql\interfaces\elements\Notification as NotificationInterface;
use percipiolondon\staff\gql\resolvers\elements\Notification as NotificationResolver;
use percipiolondon\staff\helpers\Gql as GqlHelper;

class Notifications extends Query
{
    // Public Methods
    // =========================================================================

    public static function getQueries($checkToken = true): array
    {
        return [
            'Notifications' => [
                'type' => Type::listOf(NotificationInterface::getType()),
                'args' => NotificationArguments::getArguments(),
                'resolve' => NotificationResolver::class . '::resolve',
                'description' => 'This query is used to query for all the notifications.',
                'complexity' => GqlHelper::relatedArgumentComplexity(),
            ],
            'NotificationsCount' => [
                'type' => Type::nonNull(Type::int()),
                'args' => NotificationArguments::getArguments(),
                'resolve' => NotificationResolver::class . '::resolveCount',
                'description' => 'This query is used to return the number of entries.',
                'complexity' => GqlHelper::singleQueryComplexity(),
            ],
        ];
    }
}
