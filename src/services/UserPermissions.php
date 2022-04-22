<?php
/**
 * staff-management plugin for Craft CMS 3.x
 *
 * Craft Staff Management provides an HR solution for payroll and benefits
 *
 * @link      http://percipio.london
 * @copyright Copyright (c) 2021 Percipio
 */

namespace percipiolondon\staff\services;

use Craft;
use craft\base\Component;
use percipiolondon\staff\models\UserPermission as UserPermissionModel;
use percipiolondon\staff\records\Permission as PermissionRecord;
use percipiolondon\staff\records\PermissionsUser as UserPermissionRecord;
use Throwable;
use yii\db\StaleObjectException;

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
 * @package   craft-staff
 * @since     1.0.0
 */
class UserPermissions extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * @param $permissions
     * @param $userId
     * @param $employeeId
     * @throws Throwable
     * @throws StaleObjectException
     *
     * Save a permission set of a user to the database
     */
    public function createPermissions($permissions, $userId, $employeeId): void
    {
        foreach ($permissions as $permission) {
            $record = UserPermissionRecord::findOne(['permissionId' => $permission['id'], 'employeeId' => $employeeId]);

            if (!$record) {
                $userPermission = new UserPermissionRecord();
                $userPermission->userId = $userId;
                $userPermission->employeeId = $employeeId;
                $userPermission->permissionId = $permission['id'];

                $userPermission->save();
            }
        }
    }

    /**
     * @param string $permission
     * @param int $userId
     * @return bool
     */

    public function applyCanParam(string $permission, int $userId): bool
    {
        if (!$permission || $userId) {
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
