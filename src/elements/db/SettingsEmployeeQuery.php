<?php

namespace percipiolondon\staff\elements\db;

use craft\elements\db\ElementQuery;
use craft\helpers\Db;

/**
 *
 * @property-write mixed $tingsId
 */
class SettingsEmployeeQuery extends ElementQuery
{
    public ?int $employeeId = null;
    public ?int $settingsId = null;

    public function employeeId($value)
    {
        $this->employeeId = $value;
        return $this;
    }

    public function settingsId($value)
    {
        $this->settingsId = $value;
        return $this;
    }

    protected function beforePrepare(): bool
    {
        $this->joinElementTable('staff_settings_employee');

        $this->query->select([
            'staff_settings_employee.employeeId',
            'staff_settings_employee.settingsId',
        ]);

        if ($this->employeeId) {
            $this->subQuery->andWhere(Db::parseParam('staff_settings_employee.employeeId', $this->employeeId));
        }

        if ($this->settingsId) {
            $this->subQuery->andWhere(Db::parseParam('staff_settings_employee.settingsId', $this->settingsId));
        }

//        \Craft::dd($this->query);

        return parent::beforePrepare();
    }
}
