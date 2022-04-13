<?php

namespace percipiolondon\staff\elements\db;

use craft\elements\db\ElementQuery;
use craft\helpers\Db;

use percipiolondon\staff\models\Address;

class EmployerQuery extends ElementQuery
{
    public $staffologyId;
    public $crn;

    public function staffologyId($value)
    {
        $this->staffologyId = $value;
        return $this;
    }

    public function crn($value)
    {
        $this->crn = $value;
        return $this;
    }

    protected function beforePrepare(): bool
    {
        $this->joinElementTable('staff_employers');

        $this->query->select([
            'staff_employers.staffologyId',
            'staff_employers.name',
            'staff_employers.crn',
            'staff_employers.logoUrl',
            'staff_employers.employeeCount',
            'staff_employers.startYear',
            'staff_employers.currentYear',
            'staff_employers.slug',
        ]);

        if ($this->staffologyId) {
            $this->subQuery->andWhere(Db::parseParam('staff_employers.staffologyId', $this->staffologyId));
        }

        if ($this->crn) {
            $this->subQuery->andWhere(Db::parseParam('staff_employers.crn', $this->crn));
        }

        return parent::beforePrepare();
    }
}
