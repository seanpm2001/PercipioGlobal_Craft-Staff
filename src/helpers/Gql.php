<?php

namespace percipiolondon\craftstaff\helpers;

class Gql extends \craft\helpers\Gql
{
    public static function canQueryEmployers(): bool
    {
        return true;
    }

    public static function canQueryWidgets(): bool
    {
        $allowedEntities = self::extractAllowedEntitiesFromSchema();
        return isset($allowedEntities['widgets']);
    }
}
