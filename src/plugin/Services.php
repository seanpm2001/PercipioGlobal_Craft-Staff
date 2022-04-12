<?php

namespace percipiolondon\staff\plugin;

use percipiolondon\staff\services\Addresses;
use percipiolondon\staff\services\Employees;
use percipiolondon\staff\services\Employers;
use percipiolondon\staff\services\PayRunEntries;
use percipiolondon\staff\services\PayRuns;
use percipiolondon\staff\services\Pensions;
use percipiolondon\staff\services\Totals;
use percipiolondon\staff\services\UserPermissions;

trait Services
{
    private function _setPluginComponents()
    {
        $this->setComponents([
            'addresses' => [
                'class' => Addresses::class,
            ],
            'employers' => [
                'class' => Employers::class,
            ],
            'employees' => [
                'class' => Employees::class,
            ],
            'userPermissions' => [
                'class' => UserPermissions::class,
            ],
            'payOptions' => [
                'class' => PayRuns::class,
            ],
            'payRuns' => [
                'class' => PayRuns::class,
            ],
            'payRunEntries' => [
                'class' => PayRunEntries::class,
            ],
            'pensions' => [
                'class' => Pensions::class,
            ],
            'totals' => [
                'class' => Totals::class,
            ],
        ]);
    }
}
