<?php

namespace percipiolondon\staff\gql\resolvers\elements;

use craft\gql\base\ElementResolver;

use GraphQL\Type\Definition\ResolveInfo;

use percipiolondon\staff\elements\Employee as EmployeeElement;
use percipiolondon\staff\helpers\Gql as GqlHelper;

class Employee extends ElementResolver
{

    public static function prepareQuery($source, array $arguments, $fieldName = null)
    {
        if($source === null) {
            $query = EmployeeElement::find();
        } else {
            $query = $source->$fieldName;
        }

        if(is_array($query)) {
            return $query;
        }

        foreach ($arguments as $key => $value) {
            $query->$key($value);
        }

        if (!GqlHelper::canQueryEmployees()) {
            return [];
        }

        return $query;
    }

}