<?php

namespace percipiolondon\staff\gql\resolvers\mutations;

use Craft;
use craft\gql\base\ElementMutationResolver;
use GraphQL\Type\Definition\ResolveInfo;
use percipiolondon\staff\elements\Notification as NotificationElement;

class Notification extends ElementMutationResolver
{
    public function updateNotificationViewed($source, array $arguments, $context, ResolveInfo $resolveInfo)
    {
        $notifications = NotificationElement::findAll($arguments['id']);

        if (count($notifications) > 0) {
            foreach ($notifications as $notification) {
                $notification->viewed = $arguments['viewed'];
                $this->saveElement($notification);
            }
        }

        return NotificationElement::findAll($arguments['id']);
    }
}