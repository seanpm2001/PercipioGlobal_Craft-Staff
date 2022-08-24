<?php

namespace percipiolondon\staff\gql\resolvers\elements;

use craft\gql\base\ElementResolver;

use percipiolondon\staff\elements\SettingsEmployee as SettingsEmployeeElement;
use percipiolondon\staff\helpers\Gql as GqlHelper;

class SettingsEmployee extends ElementResolver
{
    public static function prepareQuery($source, array $arguments, $fieldName = null)
    {
        if ($source === null) {
            $query = SettingsEmployeeElement::find();
        } else {
            $query = $source->$fieldName;
        }

        if (is_array($query)) {
            return $query;
        }

        foreach ($arguments as $key => $value) {
            $query->$key($value);
        }

        if (!GqlHelper::canQuerySettingsEmployee()) {
            return [];
        }

        return $query;
    }
}
