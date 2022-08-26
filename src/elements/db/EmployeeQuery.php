<?php

namespace percipiolondon\staff\elements\db;

use craft\elements\db\ElementQuery;
use craft\helpers\Db;

/**
 * Class EmployeeQuery
 *
 * @package percipiolondon\staff\elements\db
 */
class EmployeeQuery extends ElementQuery
{
    /**
     * @var
     */
    public $staffologyId;
    /**
     * @var
     */
    public $employerId;
    /**
     * @var
     */
    public $userId;
    /**
     * @var
     */
    public ?bool $isDirector = null;
    /**
     * @var
     */
    public $status;

    /**
     * @param $value
     * @return $this
     */
    public function staffologyId($value)
    {
        $this->staffologyId = $value;
        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function employerId($value)
    {
        $this->employerId = $value;
        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function userId($value)
    {
        $this->userId = $value;
        return $this;
    }

    /**
     * @param string|string[]|null $value
     * @return $this|ElementQuery|EmployeeQuery
     */
    public function status($value)
    {
        $this->status = $value;
        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function isDirector($value)
    {
        $this->isDirector = $value;
        return $this;
    }


    /**
     * @return bool
     */
    protected function beforePrepare(): bool
    {
        $this->joinElementTable('staff_employees');

        $this->query->select([
            'staff_employees.staffologyId',
            'staff_employees.employerId',
            'staff_employees.userId',
            'staff_employees.status',
            'staff_employees.niNumber',
            'staff_employees.isDirector',
        ]);

        if ($this->staffologyId) {
            $this->subQuery->andWhere(Db::parseParam('staff_employees.staffologyId', $this->staffologyId));
        }

        if ($this->employerId) {
            $this->subQuery->andWhere(Db::parseParam('staff_employees.employerId', $this->employerId));
        }

        if ($this->userId) {
            $this->subQuery->andWhere(Db::parseParam('staff_employees.userId', $this->userId));
        }

        if (!is_null($this->isDirector)) {
            $this->subQuery->andWhere(Db::parseParam('staff_employees.isDirector', $this->isDirector, '=', false));
        }

        if ($this->status) {
            $this->subQuery->andWhere(Db::parseParam('staff_employees.status', $this->status, '=', true));
        }

        return parent::beforePrepare();
    }
}
