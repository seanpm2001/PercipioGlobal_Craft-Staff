<?php

namespace percipiolondon\staff\services;

use craft\base\Component;
use percipiolondon\staff\records\Settings as SettingsRecord;
use percipiolondon\staff\records\SettingsEmployee;

class Settings extends Component
{
    public function createInitSettingsForEmployee(int $employeeId): void
    {
        $settings = SettingsRecord::find()->all();

        foreach($settings as $setting) {
            //notifications
            if(str_contains($setting['name'], 'notifications')) {
                $record = new SettingsEmployee();
                $record->settingsId = $setting['id'];
                $record->employeeId = $employeeId;
                $record->save();
            }
        }
    }
}