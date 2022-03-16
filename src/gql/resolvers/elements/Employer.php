<?php

namespace percipiolondon\staff\gql\resolvers\elements;

use craft\gql\base\ElementResolver;

use GraphQL\Type\Definition\ResolveInfo;

use percipiolondon\staff\elements\Employer as EmployerElement;
use percipiolondon\staff\helpers\Gql as GqlHelper;

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
