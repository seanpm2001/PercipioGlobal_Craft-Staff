<?php
/**
 * staff-management plugin for Craft CMS 3.x
 *
 * Craft Staff Management provides an HR solution for payroll and benefits
 *
 * @link      http://percipio.london
 * @copyright Copyright (c) 2021 Percipio
 */

namespace percipiolondon\craftstaff\services;

use percipiolondon\craftstaff\Craftstaff;

use Craft;
use craft\base\Component;
use percipiolondon\craftstaff\records\Employee;
use percipiolondon\craftstaff\records\Permission as PermissionRecord;
use percipiolondon\craftstaff\records\UserPermission as UserPermissionRecord;
use percipiolondon\craftstaff\models\UserPermission as UserPermissionModel;

/**
 * UserPermission Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    Percipio
 * @package   CompanyManagement
 * @since     0.1.0
 */
class UserPermissions extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * @param $permissions
     * @param $userId
     * @param $employeeId
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     *
     * Save a permission set of a user to the database
     */
    public function createPermissions($permissions, $userId, $employeeId)
    {
        foreach ($permissions as $permission) {

            $record = UserPermissionRecord::findOne(['permissionId' => $permission['id'], 'employeeId' => $employeeId]);

            Craft::info("createPermissions: user:{$userId }, employee:{$employeeId}, {$record?->employeeId}");

            if (!$record) {

                $userPermission = new UserPermissionModel();
                $userPermission->userId = $userId ;
                $userPermission->employeeId = $employeeId;
                $userPermission->permissionId = $permission['id'];

                $userPermission->validate();
                Craft::info("validate createPermissions: {$userPermission->validate()}");

                $record = new UserPermissionRecord();
                $record->permissionId = $userPermission->permissionId;
                $record->userId = $userPermission->userId;
                $record->employeeId = $userPermission->employeeId;
                Craft::info("record createPermissions: {$record->permissionId}, {$record->userId}, {$record->employeeId} ");

                Craft::info($record->save(true));
            }
        }
    }

    /**
     * @param $updatedPermissions
     * @param $userId
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function updatePermissions($updatedPermissions, $userId, $employeeId)
    {
        $permissions = PermissionRecord::find()->asArray()->all();
        $permissionsIds = [];

        $updatedPermissions = '' === $updatedPermissions || null === $updatedPermissions ? [] : $updatedPermissions;

        foreach ($permissions as $permission) {
            $permissionsIds[] = $permission['id'];
        }

        $userPermissions = UserPermissionRecord::findAll(['employeeId' => $employeeId]);
        $userPermissionsIds = [];

        foreach ($userPermissions as $permission) {
            $userPermissionsIds[] = $permission->permissionId;
        }

        $permissionsToSave = [];

        foreach ($permissionsIds as $permission) {

            if (in_array($permission, $userPermissionsIds) && !in_array($permission, $updatedPermissions)) {
                // Delete
                $record = UserPermissionRecord::findOne(['permissionId' => $permission, 'employeeId' => $employeeId]);
                $record->delete();
            } else if (!in_array($permission, $userPermissionsIds) && in_array($permission, $updatedPermissions)) {
                // Add
                $permissionsToSave[] = ['id' => $permission];
            }
        }

        $this->createPermissions($permissionsToSave, $userId, $employeeId);
    }

    /**
     * @param string $permission
     * @param int $userId
     * @param int $employeeId
     * @param int $employerId
     * @return bool
     */
    public function applyCanParam(string $permission, int $userId, int $employerId): bool
    {
        if (!$permission || $userId || $employerId) {
            return false;
        }

        $permission = PermissionRecord::findOne(['name' => $permission]); // fetch if permission exists in the company permissions
        $userPermission = UserPermissionRecord::findOne(['permissionId' => $permission->id, 'userId' => $userId]); // fetch if permission is assigned to the user

        // if no user permission can be fetched --> no access
        if (!$userPermission) {
            return false;
        }

        // give user access
        return true;
    }
}
