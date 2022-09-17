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
use percipiolondon\staff\elements\db\SettingsAdminQuery;
use percipiolondon\staff\records\Settings;
use percipiolondon\staff\records\SettingsAdmin as SettingsAdminRecord;

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
class SettingsAdmin extends Element
{
    // Public Properties
    // =========================================================================
    /**
     * @var int|null
     */
    public ?int $userId = null;
    /**
     * @var int|null
     */
    public ?int $settingsId = null;


    // Private Properties
    // =========================================================================
    /**
     * @var string|null
     */
    private ?string $_setting = null;
    /**
     * @var array|null
     */
    private ?array $_settings = null;


    // Static Methods
    // =========================================================================
    /**
     * @return string
     */
    public static function displayName(): string
    {
        return Craft::t('staff-management', 'Settings Admin');
    }

    /**
     * @return string
     */
    public static function lowerDisplayName(): string
    {
        return Craft::t('staff-management', 'settings admin');
    }

    /**
     * @return string
     */
    public static function pluralDisplayName(): string
    {
        return Craft::t('staff-management', 'Settings Admins');
    }

    /**
     * @return string
     */
    public static function pluralLowerDisplayName(): string
    {
        return Craft::t('staff-management', 'settings admins');
    }

    /**
     * @return ElementQueryInterface
     */
    public static function find(): ElementQueryInterface
    {
        return new SettingsAdminQuery(static::class);
    }

    /**
     * @param mixed $context
     * @return string
     */
    public static function gqlTypeNameByContext($context): string
    {
        return 'SettingAdmin';
    }

    // Public Methods
    // =========================================================================
    /**
     * @return array
     */
    public function defineRules(): array
    {
        $rules = parent::defineRules();
        $rules[] = [['userId', 'settingsId'], 'required'];

        return $rules;
    }

    // Indexes, etc.
    // -------------------------------------------------------------------------
    /**
     * @inheritdoc
     */
    public function getGqlTypeName(): string
    {
        return static::gqlTypeNameByContext($this);
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

    public static function getSettingsAdminIds(): array
    {
        $settingsIds = [];

        $settings = self::findAll(['userId' => Craft::$app->getUser()->id]);

        foreach ($settings as $setting) {
            $settingsIds[] = $setting['settingsId'];
        }

        return $settingsIds;
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


    // Private Methods
    // =========================================================================
    /**
     * @param bool $isNew
     */
    private function _saveRecord(bool $isNew): void
    {
        try {
            if (!$isNew) {
                $settingsEmployee = SettingsAdminRecord::findOne($this->id);

                if (!$settingsEmployee) {
                    throw new \Exception('Invalid settings ID: ' . $this->id);
                }
            } else {
                $settingsEmployee = new SettingsAdminRecord();
                $settingsEmployee->id = $this->id;
            }

            $setting = Settings::findOne($this->settingsId);

            if ($setting) {
                $settingsEmployee->settingsId = $this->settingsId;
                $settingsEmployee->userId = $this->userId;

                $settingsEmployee->save();
            }

        } catch (\Exception $e) {
            Craft::error($e->getMessage(), __METHOD__);
        }
    }
}