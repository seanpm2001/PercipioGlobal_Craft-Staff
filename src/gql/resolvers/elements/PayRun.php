<?php

namespace percipiolondon\staff\gql\resolvers\elements;

use craft\gql\base\ElementResolver;

use percipiolondon\staff\helpers\Gql as GqlHelper;
use percipiolondon\staff\elements\PayRun as PayRunElement;

class PayRun extends ElementResolver
{

    public static function prepareQuery($source, array $arguments, $fieldName = null)
    {
        if ($source === null) {
            $query = PayRunElement::find();
        } else {
            $query = $source->$fieldName;
        }

        if (is_array($query)) {
            return $query;
        }

        foreach ($arguments as $key => $value) {
            $query->$key($value);
        }

        if (!GqlHelper::canQueryPayruns()) {
            return [];
        }

        return $query;
    }

}