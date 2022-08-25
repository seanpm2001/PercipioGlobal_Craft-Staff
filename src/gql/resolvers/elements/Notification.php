<?php

namespace percipiolondon\staff\gql\resolvers\elements;

use craft\gql\base\ElementResolver;

use percipiolondon\staff\elements\Notification as NotificationElement;
use percipiolondon\staff\helpers\Gql as GqlHelper;

class Notification extends ElementResolver
{
    public static function prepareQuery($source, array $arguments, $fieldName = null)
    {
        if ($source === null) {
            $query = NotificationElement::find();
        } else {
            $query = $source->$fieldName;
        }

        if (is_array($query)) {
            return $query;
        }

        foreach ($arguments as $key => $value) {
            $query->$key($value);
        }

        if (!GqlHelper::canQueryNotification()) {
            return [];
        }

        return $query;
    }
}
