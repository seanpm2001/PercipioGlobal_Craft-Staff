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
use craft\elements\db\ElementQueryInterface;
use percipiolondon\staff\elements\db\NotificationQuery;
use percipiolondon\staff\Staff;
use percipiolondon\staff\records\Notifications as NotificationsRecord;

/**
 * Request Element
 *
 * Element is the base class for classes representing elements in terms of objects.
 *
 *
 * @property-read string $gqlTypeName
 * @property-read null|string $employer
 * @property-read null|string $admin
 * @property-read null|string|array $employee
 */
class Notifications extends Element
{
    public const TYPES = ['app', 'system', 'payroll', 'pension', 'employee', 'benefit'];

    /**
     * @var int|null
     */
    public ?int $employerId = null;
    /**
     * @var int|null
     */
    public ?int $employeeId = null;
    /**
     * @var string|null
     */
    public ?string $message = null;
    /**
     * @var string|null
     */
    public ?string $type = null;
    /**
     * @var bool|null
     */
    public ?bool $viewed = false;
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
        return Craft::t('staff-management', 'Notification');
    }

    /**
     * @return string
     */
    public static function lowerDisplayName(): string
    {
        return Craft::t('staff-management', 'notification');
    }

    /**
     * @return string
     */
    public static function pluralDisplayName(): string
    {
        return Craft::t('staff-management', 'Notifications');
    }

    /**
     * @return string
     */
    public static function pluralLowerDisplayName(): string
    {
        return Craft::t('staff-management', 'notifications');
    }

    /**
     * @return array
     */
    public function defineRules(): array
    {
        $rules = parent::defineRules();
        $rules[] = [['employerId', 'employeeId', 'type'], 'required'];
        $rules[] = [
            'type', function($attribute, $params) {
                if (!in_array($this->$attribute, self::TYPES, true)) {
                    $this->addError($attribute, "$attribute is not a valid type");
                }
            }
        ];

        return $rules;
    }

    /**
     * @return ElementQueryInterface
     */
    public static function find(): ElementQueryInterface
    {
        return new NotificationQuery(static::class);
    }

    // Indexes, etc.
    // -------------------------------------------------------------------------

    /**
     * @param mixed $context
     * @return string
     */
    public static function gqlTypeNameByContext($context): string
    {
        return 'Notification';
    }

    /**
     * @inheritdoc
     */
    public function getGqlTypeName(): string
    {
        return static::gqlTypeNameByContext($this);
    }

    /**
     * Returns the employer
     *
     * @return string|null
     */
    public function getEmployer(): ?array
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
        if (!$this->propagating) {
            $this->_saveRecord($isNew);
        }

        parent::afterSave($isNew);
    }

    /**
     * @param bool $isNew
     */
    private function _saveRecord(bool $isNew): void
    {
        try {
            if (!$isNew) {
                $notification = NotificationsRecord::findOne($this->id);

                if (!$notification) {
                    throw new \Exception('Invalid notification ID: ' . $this->id);
                }

            } else {
                $notification = new NotificationsRecord();
                $notification->id = $this->id;
                $notification->employerId = $this->employerId;
                $notification->employeeId = $this->employeeId;
                $notification->type = $this->type;
                $notification->message = $this->message;
            }

            // save request to the database
            $notification->viewed = $this->viewed;

            // save the request to the database
            $notification->save();
        } catch (\Exception $e) {
            Craft::error($e->getMessage(), __METHOD__);
        }
    }
}