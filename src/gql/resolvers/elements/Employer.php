<?php

namespace percipiolondon\staff\gql\resolvers\elements;

use craft\gql\base\ElementResolver;

use percipiolondon\staff\helpers\Gql as GqlHelper;
use percipiolondon\staff\elements\Employer as EmployerElement;

class Employer extends ElementResolver
{
    public static function prepareQuery($source, array $arguments, $fieldName = null)
    {
        if($source === null) {
            $query = EmployerElement::find();
        } else {
            $query = $source->$fieldName;
        }

        if(is_array($query)) {
            return $query;
        }

        foreach ($arguments as $key => $value) {
            $query->$key($value);
        }

        if (!GqlHelper::canQueryEmployers()) {
            return [];
        }

        return $query;
    }
}
