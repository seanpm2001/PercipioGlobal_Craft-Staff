<?php

namespace percipiolondon\staff\elements\db;

use craft\elements\db\ElementQuery;
use craft\helpers\Db;

class RequestQuery extends ElementQuery
{
    public $employeeId;
    public $employerId;
    public $status;
    public $type;
    public $limit;

    public function employeeId($value)
    {
        $this->employeeId = $value;
        return $this;
    }

    public function employerId($value)
    {
        $this->employerId = $value;
        return $this;
    }

    public function status($value)
    {
        $this->status = $value;
        return $this;
    }

    public function type($value)
    {
        $this->type = $value;
        return $this;
    }

    protected function beforePrepare(): bool
    {
        $this->joinElementTable('staff_requests');

        $this->query->select([
            'staff_requests.employerId',
            'staff_requests.employeeId',
            'staff_requests.administerId',
            'staff_requests.dateAdministered',
            'staff_requests.request',
            'staff_requests.data',
            'staff_requests.current',
            'staff_requests.type',
            'staff_requests.status',
            'staff_requests.note',
        ]);

        if ($this->employeeId) {
            $this->subQuery->andWhere(Db::parseParam('staff_requests.employeeId', $this->employeeId));
        }

        if ($this->employerId) {
            $this->subQuery->andWhere(Db::parseParam('staff_requests.employerId', $this->employerId));
        }

        if ($this->status) {
            $this->subQuery->andWhere(Db::parseParam('staff_requests.status', $this->status));
        }

        if ($this->type) {
            $this->subQuery->andWhere(Db::parseParam('staff_requests.type', $this->type));
        }

        return parent::beforePrepare();
    }
}
