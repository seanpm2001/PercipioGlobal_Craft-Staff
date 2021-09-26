<?php

namespace percipiolondon\craftstaff\elements\db;

use craft\elements\db\ElementQuery;
use percipiolondon\companymanagement\elements\Department;

class EmployerQuery extends ElementQuery
{
    public $slug;
    public $staffologyId;
    public $name;
//    public $logoId;
    public $crn;
    public $address;
    public $hmrcDetails;
    public $startYear;
    public $currentYear;
    public $employeeCount;
    public $defaultPayOptions;

    public function slug($value)
    {
        $this->slug = $value;
        return $this;
    }

    public function staffologyId($value)
    {
        $this->staffologyId = $value;
        return $this;
    }

    public function name($value)
    {
        $this->name = $value;
        return $this;
    }

//    public function logoId($value)
//    {
//        $this->logoId = $value;
//        return $this;
//    }

    public function crn($value)
    {
        $this->crn = $value;
        return $this;
    }

    public function address($value)
    {
        $this->address = $value;
        return $this;
    }

    public function hmrcDetails($value)
    {
        $this->hmrcDetails = $value;
        return $this;
    }

    public function startYear($value)
    {
        $this->startYear = $value;
        return $this;
    }

    public function currentYear($value)
    {
        $this->currentYear = $value;
        return $this;
    }

    public function employeeCount($value)
    {
        $this->employeeCount = $value;
        return $this;
    }

    public function defaultPayOptions($value)
    {
        $this->defaultPayOptions = $value;
        return $this;
    }


    public function status($value)
    {
        return parent::status($value);
    }

    protected function beforePrepare(): bool
    {
        $this->joinElementTable('staff_employers');

//        $employers = (new Query())
//            ->from('{{%staff_employers}}')
//            ->select('*')
//            ->all();


        $this->query->select([
            'staff_employers.slug',
            'staff_employers.staffologyId',
            'staff_employers.name',
//            'staff_employers.logoId',
            'staff_employers.crn',
            'staff_employers.address',
            'staff_employers.hmrcDetails',
            'staff_employers.startYear',
            'staff_employers.currentYear',
            'staff_employers.employeeCount',
            'staff_employers.defaultPayOptions',
        ]);

//        ["staffologyId"]=>
//  string(36) "64c5de94-7462-4bc6-a0e8-86a1bd52277d"

        return parent::beforePrepare();
    }
}
