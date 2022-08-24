<?php

namespace percipiolondon\staff\gql\resolvers\elements;

use craft\gql\base\ElementResolver;

use percipiolondon\staff\records\Settings as SettingsRecord;
use percipiolondon\staff\helpers\Gql as GqlHelper;

class Settings extends ElementResolver
{
    public static function prepareQuery($source, array $arguments, $fieldName = null)
    {
        if ($source === null) {
            $query = SettingsRecord::find();
        } else {
            $query = $source->$fieldName;
        }

        if (is_array($query)) {
            return $query;
        }

        foreach ($arguments as $key => $value) {
            $query->$key($value);
        }

        if (!GqlHelper::canQuerySettings()) {
            return [];
        }

        return $query->all();
    }
}
