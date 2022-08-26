<?php

namespace percipiolondon\staff\jobs;

use Craft;
use craft\queue\BaseJob;
use percipiolondon\staff\elements\SettingsEmployee;

class CreateSettingsJob extends BaseJob
{
    public array $criteria = [];

    public function execute($queue): void
    {
        $settingEmployee = new SettingsEmployee();
        $settingEmployee->employeeId = $this->criteria['employeeId'];
        $settingEmployee->settingsId = $this->criteria['settingsId'];

        Craft::$app->getElements()->saveElement($settingEmployee);
    }
}
