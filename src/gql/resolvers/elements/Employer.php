<?php

namespace percipiolondon\craftstaff\gql\resolvers\elements;

use craft\gql\base\ElementResolver;
use percipiolondon\craftstaff\elements\db\EmployerQuery;
use percipiolondon\craftstaff\helpers\Gql as GqlHelper;
use percipiolondon\craftstaff\elements\Employer as EmployerElement;
use yii\db\Query;

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
