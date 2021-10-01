<?php

namespace  percipiolondon\craftstaff\models;

use craft\base\Model;
use percipiolondon\craftstaff\Craftstaff;

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
//            return Craftstaff::$plugin->userPermissions->applyCanParam($this->permissionId, $this->userId, $companyId);
//        }
//
//        return false;
//    }
}
