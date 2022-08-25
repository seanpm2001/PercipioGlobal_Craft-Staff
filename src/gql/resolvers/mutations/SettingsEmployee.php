<?php

namespace percipiolondon\staff\gql\resolvers\mutations;

use Craft;
use craft\gql\base\ElementMutationResolver;
use GraphQL\Error\UserError;
use GraphQL\Type\Definition\ResolveInfo;

use percipiolondon\staff\elements\SettingsEmployee as SettingsEmployeeElement;
use percipiolondon\staff\records\Settings;

class SettingsEmployee extends ElementMutationResolver
{
    protected $immutableAttributes = ['id', 'uid'];

    public function setSettingsEmployee($source, array $arguments, $context, ResolveInfo $resolveInfo): array
    {
        $elementService = Craft::$app->getElements();
        $currentSettings = SettingsEmployeeElement::findAll(['employeeId' => $arguments['employeeId']]);
        $settings = [];

        // fetch each setting to see if it exists
        foreach ($arguments['settings'] as $setting) {
            $setting = Settings::findOne($setting);

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
                $settingEmployee = $elementService->createElement(SettingsEmployeeElement::class);
                $arrSetting = [
                    'employeeId' => $arguments['employeeId'],
                    'settingsId' => $setting
                ];
                $settingEmployee = $this->populateElementWithData($settingEmployee, $arrSetting);
                $this->saveElement($settingEmployee);
            }
        }

        return SettingsEmployeeElement::findAll(['employeeId' => $arguments['employeeId']]);
    }

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