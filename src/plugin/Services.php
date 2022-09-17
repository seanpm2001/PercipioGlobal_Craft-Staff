<?php

namespace percipiolondon\staff\plugin;

use percipiolondon\staff\services\Addresses;
use percipiolondon\staff\services\Benefits;
use percipiolondon\staff\services\Employees;
use percipiolondon\staff\services\Employers;
use percipiolondon\staff\services\History;
use percipiolondon\staff\services\Notifications;
use percipiolondon\staff\services\PayOptions;
use percipiolondon\staff\services\PayRunEntries;
use percipiolondon\staff\services\PayRuns;
use percipiolondon\staff\services\Pensions;
use percipiolondon\staff\services\Requests;
use percipiolondon\staff\services\Settings;
use percipiolondon\staff\services\Totals;
use percipiolondon\staff\services\UserPermissions;
use percipiolondon\staff\Staff;

trait Services
{
    private function _setPluginComponents()
    {
        $this->setComponents([
            'addresses' => Addresses::class,
            'employers' => Employers::class,
            'employees' => Employees::class,
            'benefits' => Benefits::class,
            'history' => History::class,
            'notifications' => Notifications::class,
            'payOptions' => PayOptions::class,
            'payRuns' => PayRuns::class,
            'payRunEntries' => PayRunEntries::class,
            'pensions' => Pensions::class,
            'requests'  => Requests::class,
            'settings' => Staff::$plugin->getSettings(),
            'staffSettings'  => Settings::class,
            'totals' => Totals::class,
            'userPermissions' => UserPermissions::class,
        ]);
    }
}
