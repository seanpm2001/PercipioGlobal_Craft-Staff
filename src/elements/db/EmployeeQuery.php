<?php

namespace percipiolondon\staff\elements\db;

use craft\db\Query;
use craft\db\QueryAbortedException;
use craft\db\Table;
use craft\elements\db\ElementQuery;
use craft\helpers\Db;

use yii\db\Connection;

class EmployeeQuery extends ElementQuery
{
    public $staffologyId;
    public $employerId;
    public $userId;
    public $personalDetailsId;
    public $employmentDetailsId;
    public $autoEnrolment;
    public $leaveSettingsId;
    public $rightToWork;
    public $bankDetailsId;
    public $status;
    public $autoEnrolmentId;
    public $niNumber;
    public $sourceSystemId;
    public $isDirector;

    /**
     * @inheritdoc
     */
    public function __construct($elementType, array $config = [])
    {
        parent::__construct($elementType, $config);
    }

    public function personalDetailsId($value)
    {
        $this->personalDetailsId = $value;
        return $this;
    }
    public function staffologyId($value)
    {
        $this->staffologyId = $value;
        return $this;
    }
    public function employerId($value)
    {
        $this->employerId = $value;
        return $this;
    }
    public function userId($value)
    {
        $this->userId = $value;
        return $this;
    }
    public function employmentDetailsId($value)
    {
        $this->employmentDetailsId = $value;
        return $this;
    }
    public function autoEnrolment($value)
    {
        $this->autoEnrolment = $value;
        return $this;
    }
    public function leaveSettingsId($value)
    {
        $this->leaveSettingsId = $value;
        return $this;
    }
    public function rightToWork($value)
    {
        $this->rightToWork = $value;
        return $this;
    }
    public function bankDetails($value)
    {
        $this->bankDetails = $value;
        return $this;
    }
    public function sourceSystemId($value)
    {
        $this->sourceSystemId = $value;
        return $this;
    }
    public function status($value)
    {
        $this->status = $value;
        return $this;
    }

    public function aeNotEnroledWarning($value)
    {
        $this->aeNotEnroledWarning = $value;
        return $this;
    }

    public function niNumber($value)
    {
        $this->niNumber = $value;
        return $this;
    }

    public function isDirector($value)
    {
        $this->isDirector = $value;
        return $this;
    }

    protected function beforePrepare(): bool
    {
        $this->joinElementTable('staff_employees');

        $this->query->select([
            'staff_employees.personalDetailsId',
            'staff_employees.staffologyId',
            'staff_employees.employerId',
            'staff_employees.userId',
            'staff_employees.personalDetailsId',
            'staff_employees.employmentDetailsId',
//            'staff_employees.autoEnrolmentId',
//            'staff_employees.leaveSettingsId',
//            'staff_employees.rightToWorkId',
//            'staff_employees.bankDetailsId',
            'staff_employees.status',
//            'staff_employees.aeNotEnroledWarning',
            'staff_employees.niNumber',
//            'staff_employees.sourceSystemId',
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

        if ($this->isDirector) {
            $this->subQuery->andWhere(Db::parseParam('staff_employees.isDirector', $this->isDirector, '=', false));
        }

        if ($this->status) {
            $this->subQuery->andWhere(Db::parseParam('staff_employees.status', $this->status, '=', true));
        }

        return parent::beforePrepare();
    }
}
