<?php

namespace percipiolondon\staff\plugin;

use percipiolondon\staff\services\Employees;
use percipiolondon\staff\services\Employers;
use percipiolondon\staff\services\PayRun;
use percipiolondon\staff\services\UserPermissions;

trait Services
{
    private function _setPluginComponents()
    {
        $this->setComponents([
            'employers' => [
                'class' => Employers::class,
            ],
            'employees' => [
                'class' => Employees::class,
            ],
            'userPermissions' => [
                'class' => UserPermissions::class,
            ],
            'payRun' => [
                'class' => PayRun::class,
            ],
        ]);
    }
}
