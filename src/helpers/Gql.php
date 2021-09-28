<?php

namespace percipiolondon\craftstaff\helpers;

use jamesedmonston\graphqlauthentication\GraphqlAuthentication;
use percipiolondon\craftstaff\Craftstaff;

class Gql extends \craft\helpers\Gql
{
    public static function canQueryEmployers(): bool
    {
//        if(\Craft::$app->getGql()->getActiveSchema()->name === 'Full Schema') {
//            return true;
//        } else {
            // access:employers
            $restrictionService = GraphqlAuthentication::$restrictionService;

            if ($restrictionService->shouldRestrictRequests()) {

                $user = GraphqlAuthentication::$tokenService->getUserFromToken();

                if (!Craftstaff::$plugin->userPermissions->applyCanParam("access:employers", $user->id) ) {
                    return false;
                }

                return true;
            }

            return true;
//        }
//
//        return false;
    }

    public static function canQueryEmployees(): bool
    {
//        if(\Craft::$app->getGql()->getActiveSchema()->name === 'Full Schema') {
//            return true;
//        } else {
        // access:employers
        $restrictionService = GraphqlAuthentication::$restrictionService;

        if ($restrictionService->shouldRestrictRequests()) {

            $user = GraphqlAuthentication::$tokenService->getUserFromToken();

            if (!Craftstaff::$plugin->userPermissions->applyCanParam("access:employees", $user->id) ) {
                return false;
            }

            return true;
        }

        return true;
//        }
//
//        return false;
    }

    public static function canQueryWidgets(): bool
    {
        $allowedEntities = self::extractAllowedEntitiesFromSchema();
        return isset($allowedEntities['widgets']);
    }
}
