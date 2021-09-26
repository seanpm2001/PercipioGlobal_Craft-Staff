<?php

namespace percipiolondon\craftstaff\elements\db;

use craft\elements\db\ElementQuery;
use percipiolondon\companymanagement\elements\Department;

class EmployeeQuery extends ElementQuery
{
    public $personalDetails;

    public function personalDetails($value)
    {
        $this->personalDetails = $value;
        return $this;
    }

    public function status($value)
    {
        return parent::status($value);
    }

    protected function beforePrepare(): bool
    {
        $this->joinElementTable('staff_employees');

        $this->query->select([
            'staff_employees.personalDetails',
        ]);

        return parent::beforePrepare();
    }
}
