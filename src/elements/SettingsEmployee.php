<?php
/**
 * staff-management plugin for Craft CMS 3.x
 *
 * Craft Staff Management provides an HR solution for payroll and benefits
 *
 * @link      http://percipio.london
 * @copyright Copyright (c) 2021 Percipio
 */

namespace percipiolondon\staff\elements;

use Craft;
use craft\base\Element;
use craft\elements\db\ElementQueryInterface;
use percipiolondon\staff\elements\db\SettingsEmployeeQuery;
use percipiolondon\staff\records\Settings;
use percipiolondon\staff\Staff;
use percipiolondon\staff\records\SettingsEmployee as SettingsEmployeeRecord;

/**
 * Request Element
 *
 * Element is the base class for classes representing elements in terms of objects.
 *
 *
 * @property-read string $gqlTypeName
 * @property-read null|string $setting
 * @property-read null|array $settings
 * @property-read null|string|array $employee
 */
class SettingsEmployee extends Element
{
    /**
     * @var int|null
     */
    public ?int $employeeId = null;
    /**
     * @var int|null
     */
    public ?int $settingsId = null;
    /**
     * @var array|null
     */
    private ?array $_employee = null;
    /**
     * @var string|null
     */
    private ?string $_setting = null;
    /**
     * @var array|null
     */
    private ?array $_settings = null;

    /**
     * @return string
     */
    public static function displayName(): string
    {
        return Craft::t('staff-management', 'Settings Employee');
    }

    /**
     * @return string
     */
    public static function lowerDisplayName(): string
    {
        return Craft::t('staff-management', 'settings employee');
    }

    /**
     * @return string
     */
    public static function pluralDisplayName(): string
    {
        return Craft::t('staff-management', 'Settings Employees');
    }

    /**
     * @return string
     */
    public static function pluralLowerDisplayName(): string
    {
        return Craft::t('staff-management', 'settings employees');
    }

    /**
     * @return array
     */
    public function defineRules(): array
    {
        $rules = parent::defineRules();
        $rules[] = [['employeeId', 'settingsId'], 'required'];

        return $rules;
    }

    /**
     * @return ElementQueryInterface
     */
    public static function find(): ElementQueryInterface
    {
        return new SettingsEmployeeQuery(static::class);
    }

    // Indexes, etc.
    // -------------------------------------------------------------------------

    /**
     * @param mixed $context
     * @return string
     */
    public static function gqlTypeNameByContext($context): string
    {
        return 'SettingEmployee';
    }

    /**
     * @inheritdoc
     */
    public function getGqlTypeName(): string
    {
        return static::gqlTypeNameByContext($this);
    }

    /**
     * @inheritdoc
     */
    public static function gqlMutationNameByContext(mixed $context): string
    {
        return 'CreateMutation';
    }

    /**
     * Returns the employer
     *
     * @return string|null
     */
    public function getEmployee(): ?array
    {
        if ($this->_employee === null) {
            if ($this->employeeId === null) {
                return null;
            }

            if (($this->_employee = Staff::$plugin->employees->getEmployeeById($this->employeeId)) === null) {
                // The author is probably soft-deleted. Just no author is set
                $this->_employee = null;
            }
        }

        return $this->_employee ?: null;
    }

    /**
     * Returns the employer
     *
     * @return string|null
     */
    public function getSetting(): ?string
    {
        if ($this->_setting === null) {
            if ($this->settingsId === null) {
                return null;
            }

            if (($this->_setting = (Settings::findOne($this->settingsId)->name ?? null)) === null) {
                // The author is probably soft-deleted. Just no author is set
                $this->_setting = null;
            }
        }

        return $this->_setting;
    }

    /**
     * Returns the employer
     *
     * @return array|null
     */
    public function getSettings(): ?array
    {
        if ($this->_settings === null) {
            return Settings::find()->all();
        }

        return null;
    }

    /**
     * @param bool $isNew
     */
    public function afterSave(bool $isNew): void
    {
        if (!$this->propagating) {
            $this->_saveRecord($isNew);
        }

        parent::afterSave($isNew);
    }

    /**
     * @param bool $isNew
     */
    private function _saveRecord(bool $isNew): void
    {
        try {
            $settingsEmployee = null;

            if (!$isNew) {
                $settingsEmployee = SettingsEmployeeRecord::findOne($this->id);

                if (!$settingsEmployee) {
                    throw new \Exception('Invalid settings ID: ' . $this->id);
                }
            } else {
                $settingsEmployee = new SettingsEmployeeRecord();
                $settingsEmployee->id = $this->id;
            }

            $setting = Settings::findOne($this->settingsId);

            if ($setting) {
                $settingsEmployee->settingsId = $this->settingsId;
                $settingsEmployee->employeeId = $this->employeeId;

                $settingsEmployee->save();
            }

        } catch (\Exception $e) {
            Craft::error($e->getMessage(), __METHOD__);
        }
    }
}