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
use craft\helpers\DateTimeHelper;
use DateTime;
use craft\base\Element;
use craft\elements\db\ElementQueryInterface;
use percipiolondon\staff\elements\db\RequestQuery;
use percipiolondon\staff\helpers\requests\CreateAddressRequest;
use percipiolondon\staff\helpers\requests\CreatePersonalDetailsRequest;
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
     * @throws InvalidConfigException if [[employerId]] is set but invalid
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
     * @throws InvalidConfigException if [[employerId]] is set but invalid
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

        return null;
    }

    /**
     * @return string|null
     */
//    public function getCurrent(): ?string
//    {
//        if($this->_current === null) {
//            if($this->type === null) {
//                return null;
//            }
//
//            $helper = null;
//            $current = null;
//
//            switch ($this->type) {
//                case 'address':
//                    $helper = new CreateAddressRequest();
//                    $current = $helper->current($this->employeeId);
//                    break;
//                case 'personal_details':
//                    $helper = new CreatePersonalDetailsRequest();
//                    $current = $helper->current($this->employeeId);
//                    break;
//            };
//
//            return $current;
//        }
//    }

    /**
     * @param bool $isNew
     */
    public function afterSave(bool $isNew): void
    {
        if (!$this->propagating) {
            $this->_saveRecord($isNew);
        }

        parent::afterSave($isNew); // TODO: Change the autogenerated stub
    }

    /**
     * @param bool $isNew
     */
    private function _saveRecord(bool $isNew): void
    {
        try {
            $request = null;
            $helper = null;

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
            if ($this->status !== 'approved') {
                switch ($this->type) {
                    case 'address':
                        $helper = new CreateAddressRequest();
                        $request->current = $helper->current($this->employeeId);
                        $request->request = $helper->create($this->data, $this->employeeId);
                        break;
                    case 'personal_details':
                        $helper = new CreatePersonalDetailsRequest();
                        $request->current = $helper->current($this->employeeId);
                        $request->request = $helper->create($this->data, $this->employeeId);
                        break;
                };
            }

            // save the request to the database
            $save = $request->save();

            // sync with Staffology if the request has been approved
            if($request->status === "approved") {
                Staff::$plugin->requests->saveToStaffology($request);
            }
        } catch (\Exception $e) {
            Craft::error($e->getMessage(), __METHOD__);
        }
    }
}