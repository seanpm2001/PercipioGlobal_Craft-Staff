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

use craft\helpers\App;
use percipiolondon\staff\elements\db\EmployeeQuery;
use percipiolondon\staff\helpers\Logger;
use percipiolondon\staff\helpers\Security as SecurityHelper;
use percipiolondon\staff\records\Employee as EmployeeRecord;
use percipiolondon\staff\records\Permission;
use percipiolondon\staff\Staff;

use yii\base\InvalidConfigException;
use yii\db\Exception;

/**
 * Employee Element
 */

class Employee extends Element
{
    // Public Properties
    // =========================================================================
    /**
     * @var string|null
     */
    public ?string $staffologyId = null;
    /**
     * @var int|null
     */
    public ?int $employerId = null;
    /**
     * @var int|null
     */
    public ?int $userId = null;
    /**
     * @var string|null
     */
    public ?string $status = null;
    /**
     * @var string|null
     */
    public ?string $niNumber = null;
    /**
     * @var array|null
     */
    public ?array $personalDetailsObject = null;
    /**
     * @var bool|null
     */
    public ?bool $isDirector = null;


    // Private Properties
    // =========================================================================
    /**
     * @var array|null|bool
     */
    private null|array|bool $_employmentDetails = null;
    /**
     * @var array|null
     */
    private ?array $_leaveSettings = null;
    /**
     * @var array|null
     */
    private ?array $_personalDetails = null;
    /**
     * @var array|null
     */
    private ?array $_employer = null;


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
     * @param mixed $context
     * @return string
     */
    public static function gqlTypeNameByContext($context): string
    {
        return 'Employee';
    }

    // Public Methods
    // =========================================================================
    /**
     * Returns the employment details
     *
     * @return bool|array|null
     */
    public function getEmploymentDetails(): bool|array|null
    {
        if ((is_null($this->_employmentDetails)) && ($this->_employmentDetails = Staff::$plugin->employees->getEmploymentDetailsByEmployee($this->id)) === null) {
            // The author is probably soft-deleted. Just no author is set
            $this->_employmentDetails = false;
        }

        return $this->_employmentDetails ?: null;
    }

    /**
     * Returns the leave settings
     *
     * @return array|null
     * @throws InvalidConfigException if [[employerId]] is set but invalid
     */
    public function getLeaveSettings(): ?array
    {
        if ($this->_leaveSettings === null) {

            if (($this->_leaveSettings = Staff::$plugin->employees->getLeaveSettingsByEmployee($this->id)) === null) {
                // The author is probably soft-deleted. Just no author is set
                $this->_leaveSettings = null;
            }
        }

        return $this->_leaveSettings ?: null;
    }

    /**
     * Returns the personal details
     *
     * @return array|null
     * @throws InvalidConfigException if [[employerId]] is set but invalid
     */
    public function getPersonalDetails(): ?array
    {
        if ($this->_personalDetails === null) {

            if (($this->_personalDetails = Staff::$plugin->employees->getPersonalDetailsByEmployee($this->id)) === null) {
                // The author is probably soft-deleted. Just no author is set
                $this->_personalDetails = null;
            }
        }

        return $this->_personalDetails ?: null;
    }

    /**
     * Returns the company name
     *
     * @return array|null
     * @throws InvalidConfigException if [[employerId]] is set but invalid
     */
    public function getEmployer(): ?array
    {
        if ($this->_employer === null) {

            if (($this->_employer = Staff::$plugin->employers->getEmployerById($this->employerId, true)) === null) {
                // The author is probably soft-deleted. Just no author is set
                $this->_employer = null;
            }
        }

        return $this->_employer ?: null;
    }

    // Indexes, etc.
    // -------------------------------------------------------------------------

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
     * @throws \Throwable
     */
    public function afterSave(bool $isNew): void
    {
        $this->_saveRecord($isNew);

        parent::afterSave($isNew);
    }

    /**
     * Saved the employee record
     * @param $isNew
     * @throws \Throwable
     */
    private function _saveRecord($isNew): void
    {
        try {
            $record = EmployeeRecord::findOne($this->id);

            if (!$record) {
                $record = new EmployeeRecord();
                $record->id = $this->id;
            }

            // user creation
            if ($this->personalDetailsObject) {
                $email = $this->personalDetailsObject['email'] ?? '';
                $user = null;

                if($email) {
                    $user = User::findOne(['email' => $email]);
                }

                // check if user exists, if so, skip this step
                if (!$user && $email) {

                    //create user
                    $user = new User();
                    $user->firstName = $this->personalDetailsObject['firstName'] ?? '';
                    $user->lastName = $this->personalDetailsObject['lastName'] ?? '';
                    $user->username = $email;
                    $user->email = $email;

                    $success = Craft::$app->elements->saveElement($user, true);

                    if (!$success) {
                        throw new Exception("The user couldn't be created");
                    }

                    Craft::info("Craft Staff: new user creation: " . $user->id);

                    // assign user to group
                    $group = Craft::$app->getUserGroups()->getGroupByHandle('hardingUsers');
                    Craft::$app->getUsers()->assignUserToGroups($user->id, [$group->id]);

                    // send activation mail
                    if(!App::parseEnv('$HUB_PREVENT_MAILS')) {
                        Craft::$app->users->sendActivationEmail($user);
                    }
                }

                //assign the userId to the employee record
                $this->userId = $user->id ?? null;
            }

            $record->employerId = $this->employerId ?? null;
            $record->staffologyId = $this->staffologyId;
            $record->status = $this->status;
            $record->niNumber = SecurityHelper::encrypt($this->niNumber ?? '');
            $record->userId = $this->userId;
            $record->isDirector = $this->isDirector;

            $record->save();

            if ($isNew) {
                // assign permissions to employee
                if ($this->isDirector) {
                    $permissions = Permission::find()->all();
                } else {
                    $permissions = [Permission::findOne(['name' => 'access:employer'])];
                }

                Staff::$plugin->userPermissions->createPermissions($permissions, $this->userId, $this->id);

                // create settings for this employee
                Staff::$plugin->staffSettings->createInitSettingsForEmployee($this->id);
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
