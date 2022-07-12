<?php

namespace percipiolondon\staff\elements\db;

use craft\elements\db\ElementQuery;
use craft\helpers\Db;

class RequestQuery extends ElementQuery
{
    public $status;
    public $employeeId;

    public function status($value)
    {
        $this->status = $value;
        return $this;
    }

    public function employeeId($value)
    {
        $this->employeeId = $value;
        return $this;
    }

    protected function beforePrepare(): bool
    {
        $this->joinElementTable('staff_requests');

        $this->query->select([
            'staff_requests.employerId',
            'staff_requests.employeeId',
            'staff_requests.administerId',
            'staff_requests.data',
            'staff_requests.type',
            'staff_requests.status',
            'staff_requests.note',
        ]);

        if ($this->status) {
            $this->subQuery->andWhere(Db::parseParam('staff_requests.status', $this->status));
        }

        if ($this->employeeId) {
            $this->subQuery->andWhere(Db::parseParam('staff_requests.employeeId', $this->employeeId));
        }

        return parent::beforePrepare();
    }
}
