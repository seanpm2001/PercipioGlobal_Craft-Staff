<?php

namespace percipiolondon\staff\helpers;

use craft\helpers\Gql as CraftGql;

class Gql extends CraftGql
{
    // QUERIES
    /**
     * @throws \craft\errors\GqlException
     */
    public static function canQueryBenefitProviders(): bool
    {
        return CraftGql::canSchema('benefit-providers');
    }
    /**
     * @throws \craft\errors\GqlException
     */
    public static function canQueryGroupBenefits(): bool
    {
        return CraftGql::canSchema('group-benefits');
    }

    /**
     * @throws \craft\errors\GqlException
     */
    public static function canQueryEmployers(): bool
    {
        return CraftGql::canSchema('employers');
    }


    /**
     * @throws \craft\errors\GqlException
     */
    public static function canQueryEmployees(): bool
    {
        return CraftGql::canSchema('employees');
    }


    /**
     * @throws \craft\errors\GqlException
     */
    public static function canQueryHistory(): bool
    {
        return CraftGql::canSchema('history');
    }


    /**
     * @throws \craft\errors\GqlException
     */
    public static function canQueryNotification(): bool
    {
        return CraftGql::canSchema('notifications');
    }


    /**
     * @throws \craft\errors\GqlException
     */
    public static function canQueryPayruns(): bool
    {
        return CraftGql::canSchema('pay-runs');
    }


    /**
     * @throws \craft\errors\GqlException
     */
    public static function canQueryPayrunEntries(): bool
    {
        return CraftGql::canSchema('pay-run-entries');
    }


    /**
     * @throws \craft\errors\GqlException
     */
    public static function canQueryRequests(): bool
    {
        return CraftGql::canSchema('requests');
    }


    /**
     * @throws \craft\errors\GqlException
     */
    public static function canQuerySettingsEmployee(): bool
    {
        return CraftGql::canSchema('settings-employee');
    }


    /**
     * @throws \craft\errors\GqlException
     */
    public static function canQuerySettings(): bool
    {
        return CraftGql::canSchema('settings');
    }

    // MUTATIONS

    /**
     * @throws \craft\errors\GqlException
     */
    public static function canMutateBenefitEmployees(): bool
    {
        return CraftGql::canSchema('benefit-employees','create');
    }

    /**
     * @throws \craft\errors\GqlException
     */
    public static function canMutateNotifications(): bool
    {
        return CraftGql::canSchema('notifications','update');
    }

    /**
     * @throws \craft\errors\GqlException
     */
    public static function canMutateRequests(): bool
    {
        return CraftGql::canSchema('requests','create');
    }

    /**
     * @throws \craft\errors\GqlException
     */
    public static function canMutateSettingsEmployee(): bool
    {
        return CraftGql::canSchema('settings-employee','update');
    }
}
