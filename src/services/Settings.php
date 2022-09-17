<?php

namespace percipiolondon\staff\services;

use Craft;
use craft\base\Component;
use percipiolondon\staff\elements\Employee;
use percipiolondon\staff\elements\SettingsAdmin;
use percipiolondon\staff\records\Settings as SettingsRecord;
use percipiolondon\staff\elements\SettingsEmployee;

/**
 * Class Settings
 *
 * @package percipiolondon\staff\services
 */
class Settings extends Component
{
    // Public Methods
    // =========================================================================
    /**
     * @param int $employeeId
     * @throws \Throwable
     * @throws \craft\errors\ElementNotFoundException
     * @throws \yii\base\Exception
     */
    public function createInitSettingsForEmployee(int $employeeId): void
    {
        $settings = SettingsRecord::find()->all();
        $employee = Employee::findOne($employeeId);

        if ($employee) {
            foreach ($settings as $setting) {
                //notifications
                if (str_contains($setting['name'], 'notifications')) {
                    $record = new SettingsEmployee();
                    $record->settingsId = $setting['id'];
                    $record->employeeId = $employeeId;

                    Craft::$app->getElements()->saveElement($record);
                }

                //app
                if (str_contains($setting['name'], 'app')) {
                    $record = new SettingsEmployee();
                    $record->settingsId = $setting['id'];
                    $record->employeeId = $employeeId;

                    Craft::$app->getElements()->saveElement($record);
                }
            }
        }
    }

    /**
     * @param array $savedSettings
     * @param int $employeeId
     * @return array|null
     * @throws \Throwable
     * @throws \craft\errors\ElementNotFoundException
     * @throws \yii\base\Exception
     */
    public function setSettingsEmployee(array $savedSettings, int $employeeId): ?array
    {
        $currentSettings = SettingsEmployee::findAll(['employeeId' => $employeeId]);
        $settings = [];

        // fetch each setting to see if it exists
        foreach ($savedSettings as $setting) {
            $setting = \percipiolondon\staff\records\Settings::findOne($setting);

            if ($setting) {
                $settings[] = $setting->id;
            }
        }

        // loop through the current settings to delete settings that aren't in the arguments settings
        foreach ($currentSettings as $currentSetting) {
            if (!in_array($currentSetting->settingsId, $settings, true)) {
                Craft::$app->elements->deleteElement($currentSetting);
            }
        }

        // loop through the new settings to add the setting if it's not existing in the current settings
        foreach ($settings as $setting) {
            if (is_null($this->_settingsContains($currentSettings, $setting))) {
                $settingEmployee = new SettingsEmployee();
                $settingEmployee->employeeId = $employeeId;
                $settingEmployee->settingsId = $setting;
                Craft::$app->getElements()->saveElement($settingEmployee);
            }
        }

        return SettingsEmployee::findAll(['employeeId' => $employeeId]);
    }

    /**
     * @param array $savedSettings
     * @param int $userId
     * @return array|null
     * @throws \Throwable
     * @throws \craft\errors\ElementNotFoundException
     * @throws \yii\base\Exception
     */
    public function setSettingsAdmin(array $savedSettings, int $userId): ?array
    {
        $currentSettings = SettingsAdmin::findAll(['userId' => $userId]);
        $settings = [];

        // fetch each setting to see if it exists
        foreach ($savedSettings as $setting) {
            $setting = \percipiolondon\staff\records\Settings::findOne($setting);

            if ($setting) {
                $settings[] = $setting->id;
            }
        }


        // loop through the current settings to delete settings that aren't in the arguments settings
        foreach ($currentSettings as $currentSetting) {
            if (!in_array($currentSetting->settingsId, $settings, true)) {
                Craft::$app->elements->deleteElement($currentSetting);
            }
        }

        // loop through the new settings to add the setting if it's not existing in the current settings
        foreach ($settings as $setting) {
            if (is_null($this->_settingsContains($currentSettings, $setting))) {
                $settingEmployee = new SettingsAdmin();
                $settingEmployee->userId = $userId;
                $settingEmployee->settingsId = $setting;
                Craft::$app->getElements()->saveElement($settingEmployee);
            }
        }

        return SettingsAdmin::findAll(['userId' => $userId]);
    }

    // Private Methods
    // =========================================================================
    /**
     * @param array $settings
     * @param $id
     * @return int|null
     */
    private function _settingsContains(array $settings, $id): ?int
    {
        foreach ($settings as $setting) {
            if($setting->settingsId === $id) {
                return $id;
            }
        }

        return null;
    }


}