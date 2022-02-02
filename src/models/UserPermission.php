<?php

namespace  percipiolondon\staff\models;

use craft\base\Model;
use percipiolondon\staff\Staff;

class UserPermission extends Model
{
    /**
     * @var string|null Name
     */
    public $permissionId;
    public $employeeId;
    public $userId;

    protected function defineRules(): array
    {
        $rules = parent::defineRules();
        $rules[] = [['permissionId', 'employeeId'], 'required'];

        return $rules;
    }

//    public function can(int $companyId): bool
//    {
//        if($this->permissionId && $this->userId && $companyId)
//        {
//            return Staff::$plugin->userPermissions->applyCanParam($this->permissionId, $this->userId, $companyId);
//        }
//
//        return false;
//    }
}
