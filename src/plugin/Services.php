<?php

namespace percipiolondon\craftstaff\plugin;

use percipiolondon\craftstaff\services\Employees;
use percipiolondon\craftstaff\services\Employers;

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
        ]);
    }
}
