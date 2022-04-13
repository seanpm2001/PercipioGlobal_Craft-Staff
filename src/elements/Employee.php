<?php
/**
 * staff-management plugin for Craft CMS 3.x
 *
 * Craft Staff Management provides an HR solution for payroll and benefits
 *
 * @link      http://percipio.london
 * @copyright Copyright (c) 2021 Percipio
 */

namespace percipiolondon\staff\elements;

use Craft;
use craft\base\Element;
use craft\db\Query;
use craft\elements\db\ElementQueryInterface;
use craft\elements\User;

use percipiolondon\staff\helpers\Logger;
use percipiolondon\staff\helpers\Security as SecurityHelper;
use percipiolondon\staff\elements\db\EmployeeQuery;
use percipiolondon\staff\records\Employee as EmployeeRecord;
use percipiolondon\staff\records\Permission;
use percipiolondon\staff\records\PersonalDetails;
use percipiolondon\staff\Staff;

use yii\db\Exception;

/**
 * Employee Element
 */

class Employee extends Element
{
    // Public Properties
    // =========================================================================

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

    // Static Methods
    // =========================================================================

    /**
     * Returns the display name of this class.
     *
     * @return string The display name of this class.
     */
    public static function displayName(): string
    {
        return Craft::t('staff-management', 'Employee');
    }

    /**
     * @inheritdoc
     */
    public static function lowerDisplayName(): string
    {
        return Craft::t('staff-management', 'employee');
    }

    /**
     * @inheritdoc
     */
    public static function pluralDisplayName(): string
    {
        return Craft::t('staff-management', 'Employees');
    }

    /**
     * @inheritdoc
     */
    public static function pluralLowerDisplayName(): string
    {
        return Craft::t('staff-management', 'employees');
    }

    /**
     * Creates an [[ElementQueryInterface]] instance for query purpose.
     *
     * The returned [[ElementQueryInterface]] instance can be further customized by calling
     * methods defined in [[ElementQueryInterface]] before `one()` or `all()` is called to return
     * populated [[ElementInterface]] instances. For example,
     *
     * @return ElementQueryInterface The newly created [[ElementQueryInterface]] instance.
     */
    public static function find(): ElementQueryInterface
    {
        return new EmployeeQuery(static::class);
    }

    /**
     * Defines the sources that elements of this type may belong to.
     *
     * @param string|null $context The context ('index' or 'modal').
     *
     * @return array The sources.
     * @see sources()
     */
    protected static function defineSources(string $context = null): array
    {
        $ids = self::_getEmployeeIds();

        return [
            [
                'key' => '*',
                'label' => 'All Employees',
                'defaultSort' => ['id', 'desc'],
                'criteria' => ['id' => $ids],
            ]
        ];
    }

    // Public Methods
    // =========================================================================

    /**
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     *
     * More info: http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     *
     * @return array
     */
    public function rules()
    {
        return parent::rules();
    }

    /**
     * Returns the field layout used by this element.
     *
     * @return FieldLayout|null
     */
    public function getFieldLayout()
    {
        return null;
    }

    // Indexes, etc.
    // -------------------------------------------------------------------------

    public static function gqlTypeNameByContext($context): string
    {
        return 'Employee';
    }

    /**
     * @inheritdoc
     */
    public function getGqlTypeName(): string
    {
        return static::gqlTypeNameByContext($this);
    }

    // Events
    // -------------------------------------------------------------------------

    /**
     * Performs actions after an element is saved.
     *
     * @param bool $isNew Whether the element is brand new
     *
     * @return void
     */
    public function afterSave(bool $isNew)
    {
        $this->_saveRecord($isNew);

        return parent::afterSave($isNew);
    }

    /**
     * Performs actions before an element is deleted.
     *
     * @return bool Whether the element should be deleted
     */
    public function beforeDelete(): bool
    {
        return true;
    }

    /**
     * Performs actions after an element is deleted.
     *
     * @return void
     */
    public function afterDelete()
    {
        return true;
    }

    private function _saveRecord($isNew)
    {
        try {
            $record = EmployeeRecord::findOne($this->id);

            if ($record) {
                $record = EmployeeRecord::findOne($this->id);

                if (!$record) {
                    throw new Exception('Invalid employee ID: ' . $this->id);
                }

            } else {
                $record = new EmployeeRecord();
                $record->id = (int)$this->id;
            }

            $personalDetails = PersonalDetails::findOne($this->personalDetailsId);

            // user creation
            if($personalDetails) {

                $email = SecurityHelper::decrypt($personalDetails['email']) ?? '';
                $user = User::findOne(['email' => $email]);

                // check if user exists, if so, skip this step
                if(!$user && $email) {

                    //create user
                    $user = new User();
                    $user->firstName = SecurityHelper::decrypt($personalDetails['firstName']);
                    $user->lastName = SecurityHelper::decrypt($personalDetails['lastName']);
                    $user->username = $email;
                    $user->email = $email;

                    $success = Craft::$app->elements->saveElement($user, true);

                    if(!$success){
                        throw new Exception("The user couldn't be created");
                    }

                    Craft::info("Craft Staff: new user creation: ".$user->id);

                    // assign user to group
                    $group = Craft::$app->getUserGroups()->getGroupByHandle('hardingUsers');
                    Craft::$app->getUsers()->assignUserToGroups($user->id, [$group->id]);
                }

                //assign the userId to the employee record
                $this->userId = $user->id ?? null;
            }

            $record->employerId = $this->employerId ?? null;
            $record->staffologyId = $this->staffologyId;
            $record->personalDetailsId = $this->personalDetailsId;
            $record->employmentDetailsId = $this->employmentDetailsId;
            $record->status = $this->status;
            $record->sourceSystemId = $this->sourceSystemId;
            $record->niNumber = SecurityHelper::encrypt($this->niNumber ?? '');
            $record->userId = $this->userId;
            $record->isDirector = $this->isDirector;

            $record->save();

            if($isNew) {
                //assign permissions to employee
                if($this->isDirector) {
                    $permissions = Permission::find()->all();
                } else {
                    $permissions = [Permission::findOne(['name' => 'access:employer'])];
                }

                Staff::$plugin->userPermissions->createPermissions($permissions, $this->userId, $this->id);
            }

        } catch (\Exception $e) {

            $logger = new Logger();
            $logger->stdout(PHP_EOL, $logger::RESET);
            $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
            Craft::error($e->getMessage(), __METHOD__);
        }
    }

    /**
     * Returns all employee ID's
     *
     * @return array
     */
    private static function _getEmployeeIds(): array
    {
        $employeeIds = [];

        $employees = (new Query())
            ->from('{{%staff_employees}}')
            ->select('id')
            ->all();

        foreach ($employees as $employee) {
            $employeeIds[] = $employee['id'];
        }

        return $employeeIds;
    }
}
