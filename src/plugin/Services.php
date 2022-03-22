<?php

namespace percipiolondon\staff\plugin;

use percipiolondon\staff\services\Addresses;
use percipiolondon\staff\services\Employees;
use percipiolondon\staff\services\Employers;
use percipiolondon\staff\services\PayRuns;
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
            'payRuns' => [
                'class' => PayRuns::class,
            ],
        ]);
    }
}
