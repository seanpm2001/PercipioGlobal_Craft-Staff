<?php

namespace percipiolondon\staff\elements\db;

use craft\elements\db\ElementQuery;
use craft\helpers\Db;

class NotificationQuery extends ElementQuery
{
    public $employeeId;
    public $employerId;
    public $type;
    public $viewed;

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

    public function type($value)
    {
        $this->type = $value;
        return $this;
    }

    public function viewed($value)
    {
        $this->viewed = $value;
        return $this;
    }

    protected function beforePrepare(): bool
    {
        $this->joinElementTable('staff_notifications');

        $this->query->select([
            'staff_notifications.employerId',
            'staff_notifications.employeeId',
            'staff_notifications.message',
            'staff_notifications.type',
            'staff_notifications.viewed',
        ]);

        if ($this->employeeId) {
            $this->subQuery->andWhere(Db::parseParam('staff_notifications.employeeId', $this->employeeId));
        }

        if ($this->employerId) {
            $this->subQuery->andWhere(Db::parseParam('staff_notifications.employerId', $this->employerId));
        }

        if ($this->type) {
            $this->subQuery->andWhere(Db::parseParam('staff_notifications.type', $this->type));
        }

        return parent::beforePrepare();
    }
}
