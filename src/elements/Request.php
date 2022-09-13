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
use craft\elements\User;
use craft\helpers\App;
use craft\helpers\DateTimeHelper;
use DateTime;
use craft\base\Element;
use craft\elements\db\ElementQueryInterface;
use percipiolondon\staff\elements\db\RequestQuery;
use percipiolondon\staff\helpers\HistoryMessages;
use percipiolondon\staff\helpers\NotificationMessage;
use percipiolondon\staff\helpers\requests\CreateAddressRequest;
use percipiolondon\staff\helpers\requests\CreatePersonalDetailsRequest;
use percipiolondon\staff\helpers\requests\CreateTelephoneRequest;
use percipiolondon\staff\records\Employee;
use percipiolondon\staff\records\Requests;
use percipiolondon\staff\Staff;

/**
 * Request Element
 *
 * Element is the base class for classes representing elements in terms of objects.
 *
 */
class Request extends Element
{
    public const STATUSES = ['approved', 'canceled', 'declined', 'pending'];

    /**
     * @var string|null
     */
    public ?string $dateAdministered = null;
    /**
     * @var int|null
     */
    public ?int $employerId = null;
    /**
     * @var int|null
     */
    public ?int $employeeId = null;
    /**
     * @var int|null
     */
    public ?int $administerId = null;
    /**
     * @var string|null
     */
    public ?string $request = null;
    /**
     * @var string|null
     */
    public ?string $data = null;
    /**
     * @var string|null
     */
    public ?string $current = null;
    /**
     * @var string|null
     */
    public ?string $type = null;
    /**
     * @var string|null
     */
    public ?string $status = null;
    /**
     * @var string|null
     */
    public ?string $note = null;
    /**
     * @var string|null
     */
    public ?string $error = null;
    /**
     * @var string|null
     */
    private ?string $_admin = null;
    /**
     * @var string|null
     */
    private ?string $_employer = null;
    /**
     * @var array|null
     */
    private ?array $_employee = null;

    /**
     * @return string
     */
    public static function displayName(): string
    {
        return Craft::t('staff-management', 'Request');
    }

    /**
     * @return string
     */
    public static function lowerDisplayName(): string
    {
        return Craft::t('staff-management', 'request');
    }

    /**
     * @return string
     */
    public static function pluralDisplayName(): string
    {
        return Craft::t('staff-management', 'Requests');
    }

    /**
     * @return string
     */
    public static function pluralLowerDisplayName(): string
    {
        return Craft::t('staff-management', 'requests');
    }

    /**
     * @return array
     */
    public function defineRules(): array
    {
        $rules = parent::defineRules();
        $rules[] = [['employerId', 'employeeId', 'type'], 'required'];
        $rules[] = ['status', function($attribute, $params) {
            if (!in_array($this->$attribute, self::STATUSES, true)) {
                $this->addError($attribute, "$attribute is not a valid type");
            }
        }];

        return $rules;
    }

    /**
     * @return ElementQueryInterface
     */
    public static function find(): ElementQueryInterface
    {
        return new RequestQuery(static::class);
    }

    // Indexes, etc.
    // -------------------------------------------------------------------------

    /**
     * @param mixed $context
     * @return string
     */
    public static function gqlTypeNameByContext($context): string
    {
        return 'Request';
    }

    /**
     * @inheritdoc
     */
    public function getGqlTypeName(): string
    {
        return static::gqlTypeNameByContext($this);
    }

    /**
     * @inheritdoc
     */
    public static function gqlMutationNameByContext(mixed $context): string
    {
        return 'CreateMutation';
    }

    /**
     * Returns the employer
     *
     * @return string|null
     * @throws InvalidConfigException if [[employerId]] is set but invalid
     */
    public function getEmployer()
    {
        if ($this->_employer === null) {
            if ($this->employerId === null) {
                return null;
            }

            if (($this->_employer = Staff::$plugin->employers->getEmployerNameById($this->employerId)) === null) {
                // The author is probably soft-deleted. Just no author is set
                $this->_employer = null;
            }
        }

        return $this->_employer ?: null;
    }

    /**
     * Returns the employer
     *
     * @return string|null
     */
    public function getAdmin()
    {
        if ($this->_admin === null) {
            if ($this->administerId === null) {
                return null;
            }

            $admin = User::findOne($this->administerId);

            if (isset($admin) > 0) {
                // The author is probably soft-deleted. Just no author is set
                $this->_admin = $admin->getFullName() ?? $admin->username ?? 'Unknown';
            }
        }

        return $this->_admin ?: null;
    }

    /**
     * Returns the employer
     *
     * @return string|null
     */
    public function getEmployee(): ?array
    {
        if ($this->_employee === null) {
            if ($this->employeeId === null) {
                return null;
            }

            if (($this->_employee = Staff::$plugin->employees->getEmployeeById($this->employeeId)) === null) {
                // The author is probably soft-deleted. Just no author is set
                $this->_employee = null;
            }
        }

        return $this->_employee ?: null;
    }

    /**
     * @param bool $isNew
     */
    public function afterSave(bool $isNew): void
    {
        $this->_saveRecord($isNew);

        parent::beforeSave($isNew);
    }

    /**
     * @param bool $isNew
     */
    private function _saveRecord(bool $isNew): bool
    {
        try {
            if(!$isNew) {
                $request = Requests::findOne($this->id);

                if(!$request) {
                    throw new \Exception('Invalid request ID: ' . $this->id);
                }

                // update the request
                if($this->status !== "pending") {
                    $request->administerId = $this->administerId;
                    $date = new DateTime('now');
                    $request->dateAdministered = $date;
                } else {
                    // reset if the state is from an accepted / declined / canceled to a pending one
                    $request->administerId = null;
                    $request->dateAdministered = null;
                }
            } else {
                $request = new Requests();
                $request->id = $this->id;
                $request->employerId = $this->employerId;
                $request->employeeId = $this->employeeId;
                $request->type = $this->type;
            }

            // save request to the database
            $request->status = $this->status ?? 'pending';
            $request->note = $this->note ?? '';
            $request->data = $this->data;

            // create the data object according to the request type if it's not an approved request

            switch ($this->type) {
                case 'address':
                    $helper = new CreateAddressRequest();
                    if ($this->status !== 'approved') {
                        $request->current = $helper->current($this->employeeId);
                    }
                    $request->request = $helper->create($this->data, $this->employeeId);
                    break;
                case 'personal_details':
                    $helper = new CreatePersonalDetailsRequest();
                    if ($this->status !== 'approved') {
                        $request->current = $helper->current($this->employeeId);
                    }
                    $request->request = $helper->create($this->data, $this->employeeId);
                    break;
                case 'telephone':
                    $helper = new CreateTelephoneRequest();
                    if ($this->status !== 'approved') {
                        $request->current = $helper->current($this->employeeId);
                    }
                    $request->request = $helper->create($this->data, $this->employeeId);
                    break;
            }

            $valid = true;

            // sync with Staffology if the request has been approved
            if($request->status === "approved") {
                $valid = Staff::$plugin->requests->saveToStaffology($request);
            }

            if ($valid) {
                // save the request to the database
                $save = $request->save();

                if ($save) {
                    $employee = Employee::findOne($request->employeeId);

                    if ($employee) {
                        // create a history log
                        Staff::$plugin->history->saveHistory($employee, 'employee', HistoryMessages::getMessage('employee', $request->type, $request->status), $request->request, $request->administerId ?? null);

                        // create a notification for the employoee
                        $notificationMessage = NotificationMessage::getNotification('employee' , $request->type, $request->status);
                        $emailMessage = NotificationMessage::getEmail('employee', $request->type, $request->status);
                        Staff::$plugin->notifications->createNotificationByEmployee($request->employeeId, 'employee', ($request->status === 'approved' || $request->status === 'declined'), $notificationMessage, $emailMessage);

                        // create an admin notification
                        if ($request->status === 'pending') {
                            foreach (User::find()->group('hardingAdmin')->all() as $user) {
                                $adminEmailMessage = NotificationMessage::getAdminEmail('employee', $request->type, $request->status);
                                $url = App::parseEnv('$SITE_URL') . '/admin/staff-management/requests/' . $request->id;
                                Staff::$plugin->notifications->sendNotificationByUser($user->id, $adminEmailMessage, ['adminUrl' => $url]);
                            }
                        }
                    }
                }
            }

            return $valid;
        } catch (\Exception $e) {
            Craft::error($e->getMessage(), __METHOD__);
        }

        return false;
    }
}