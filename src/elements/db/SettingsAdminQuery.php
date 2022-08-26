<?php

namespace percipiolondon\staff\elements\db;

use craft\elements\db\ElementQuery;
use craft\helpers\Db;

/**
 *
 * @property-write mixed $tingsId
 */
class SettingsAdminQuery extends ElementQuery
{
    public ?int $userId = null;
    public ?int $settingsId = null;

    public function userId($value)
    {
        $this->userId = $value;
        return $this;
    }

    public function settingsId($value)
    {
        $this->settingsId = $value;
        return $this;
    }

    protected function beforePrepare(): bool
    {
        $this->joinElementTable('staff_settings_admin');

        $this->query->select([
            'staff_settings_admin.userId',
            'staff_settings_admin.settingsId',
        ]);

//        \Craft::dd($this->userId);

        if ($this->userId) {
            $this->subQuery->andWhere(Db::parseParam('staff_settings_admin.userId', $this->userId));
        }

        if ($this->settingsId) {
            $this->subQuery->andWhere(Db::parseParam('staff_settings_admin.settingsId', $this->settingsId));
        }

        return parent::beforePrepare();
    }
}
