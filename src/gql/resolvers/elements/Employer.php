<?php

namespace percipiolondon\craftstaff\gql\resolvers\elements;

use craft\gql\base\ElementResolver;
use percipiolondon\craftstaff\helpers\Gql as GqlHelper;

class Employer extends ElementResolver
{
    public static function prepareQuery($source, array $arguments, $fieldName = null)
    {
        if($source === null) {
            $query = \percipiolondon\craftstaff\elements\Employer::find();
        } else {
            $query = $source->$fieldName;
        }

        if(is_array($query)) {
            return $query;
        }

        foreach ($arguments as $key => $value) {
            $query->$key($value);
        }

        $pairs = GqlHelper::extractAllowedEntitiesFromSchema('read');

        if (!GqlHelper::canQueryAssets()) {
            return [];
        }
    }
}
