<?php

namespace percipiolondon\craftstaff\plugin;

use percipiolondon\craftstaff\services\Employers;

trait Services
{
    public function getEmployers(): Employers
    {
        return $this->get('employers');
    }

    private function _setPluginComponents()
    {
        $this->setComponents([
            'employers' => [
                'class' => Employers::class,
            ],
        ]);
    }
}
