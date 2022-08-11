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
     * @var DateTime|null
     */
    public ?DateTime $dateAdministered = null;
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
    public ?string $data = null;
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
     * @var string|null
     */
    private ?string $_request = null;
    /**
     * @var string|null
     */
    private ?string $_current = null;

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

            if (count($admin) > 0) {
                // The author is probably soft-deleted. Just no author is set
                $this->_admin = $admin->fistName . ' ' . $admin->lastName;
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
    public function getRequest(): ?string
    {
        if($this->_request === null) {
            if($this->type === null) {
                return null;
            }

            $helper = null;

            match (true) {
                $this->type === 'address' => $helper = new CreateAddressRequest(),
                $this->type === 'personal_details' => $helper = new CreatePersonalDetailsRequest(),
            };

            if ($helper) {
                return $helper->parse($this->data);
            }
        }
    }

    /**
     * @return string|null
     */
    public function getCurrent(): ?string
    {
        if($this->_current === null) {
            if($this->type === null) {
                return null;
            }

            $helper = null;
            $current = null;

            switch ($this->type) {
                case 'address':
                    $helper = new CreateAddressRequest();
                    $current = $helper->current($this->employeeId);
                    break;
                case 'personal_details':
                    $helper = new CreatePersonalDetailsRequest();
                    $current = $helper->current($this->employeeId);
                    break;
            };

            return $current;
        }
    }

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
            $helper = null;
            $request = null;

            if(!$isNew) {
                $request = Requests::findOne($this->id);

                if(!$request) {
                    throw new \Exception('Invalid request ID: ' . $this->id);
                }
            } else {
                $request = new Requests();
                $request->id = $this->id;
            }

            // convert form submission to saved personal data
            match (true) {
                $this->type === 'address' => $helper = new CreateAddressRequest(),
                $this->type === 'personal_details' => $helper = new CreatePersonalDetailsRequest(),
            };

            // save request to the database
            $request->id = $this->id;
            $request->employerId = $this->employerId;
            $request->employeeId = $this->employeeId;
            $request->type = $this->type;
            $request->status = $this->status ?? 'pending';

            // create the data object according to the request type
            $request->data = $helper ? $helper->create($this->data, $this->employeeId) : null;

            $request->save();


        } catch (\Exception $e) {
            Craft::error($e->getMessage(), __METHOD__);
        }
    }
}