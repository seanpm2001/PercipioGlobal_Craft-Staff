<?php

namespace percipiolondon\staff\helpers;

class Gql extends \craft\helpers\Gql
{
    public static function canQueryEmployers(): bool
    {
        return true;
    }

    public static function canQueryEmployees(): bool
    {
        return true;
    }

    public static function canQueryPayruns(): bool
    {
        return true;
    }

    public static function canQueryPayrunEntries(): bool
    {
        return true;
    }
}
