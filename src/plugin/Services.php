<?php

namespace percipiolondon\craftstaff\plugin;

use percipiolondon\craftstaff\services\Employees;
use percipiolondon\craftstaff\services\Employers;
use percipiolondon\craftstaff\services\PayRun;
use percipiolondon\craftstaff\services\UserPermissions;

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
