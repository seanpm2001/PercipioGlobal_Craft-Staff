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

use percipiolondon\staff\elements\db\EmployerQuery;
use percipiolondon\staff\helpers\Logger;
use percipiolondon\staff\helpers\Security as SecurityHelper;

use percipiolondon\staff\records\Address;
use percipiolondon\staff\records\Employer as EmployerRecord;
use percipiolondon\staff\records\HmrcDetails;
use percipiolondon\staff\records\PayOption;
use percipiolondon\staff\Staff;
use yii\db\Exception;
use yii\db\Query;

/**
 * Employer Element
 *
 * Element is the base class for classes representing elements in terms of objects.
 *
 */
class Employer extends Element
{
    // Public Properties
    // =========================================================================
    /**
     * @var string|null
     */
    public ?string $staffologyId = null;
    /**
     * @var string|null
     */
    public ?string $name = null;
    /**
     * @var string|null
     */
    public ?string $logoUrl = null;
    /**
     * @var string|null
     */
    public ?string $crn = null;
    /**
     * @var string|null
     */
    public ?string $startYear = null;
    /**
     * @var string|null
     */
    public ?string $currentYear = null;
    /**
     * @var array|null
     */
    public ?array $defaultPayOptions = null;
    /**
     * @var string|null
     */
    public ?string $employeeCount = null;

    // Private Properties
    // =========================================================================
    /**
     * @var PayRun|null
     */
    private ?PayRun $_currentPayRun = null;
    /**
     * @var PayOption|null
     */
    private ?PayOption $_defaultPayOptions = null;
    /**
     * @var array|null
     */
    private ?array $_hmrcDetails = null;
    /**
     * @var array|null
     */
    private ?array $_address = null;


    // Static Methods
    // =========================================================================
    /**
     * Returns the display name of this class.
     *
     * @return string The display name of this class.
     */
    public static function displayName(): string
    {
        return Craft::t('staff-management', 'Employer');
    }

    /**
     * @inheritdoc
     */
    public static function lowerDisplayName(): string
    {
        return Craft::t('staff-management', 'employer');
    }

    /**
     * @inheritdoc
     */
    public static function pluralDisplayName(): string
    {
        return Craft::t('staff-management', 'Employers');
    }

    /**
     * @inheritdoc
     */
    public static function pluralLowerDisplayName(): string
    {
        return Craft::t('staff-management', 'employers');
    }

    /**
     * Creates an [[ElementQueryInterface]] instance for query purpose.
     *
     * The returned [[ElementQueryInterface]] instance can be further customized by calling
     * methods defined in [[ElementQueryInterface]] before `one()` or `all()` is called to return
     * populated [[ElementInterface]] instances. For example,
     *
     *
     * @return ElementQueryInterface The newly created [[ElementQueryInterface]] instance.
     */
    public static function find(): ElementQueryInterface
    {
        return new EmployerQuery(static::class);
    }

    /**
     * @param mixed $context
     * @return string
     */
    public static function gqlTypeNameByContext($context): string
    {
        return 'Employer';
    }


    // Public Methods
    // =========================================================================
    /**
     * Returns the payrun totals.
     *
     * @return PayRun|null
     */
    public function getCurrentPayRun(): ?PayRun
    {
        if ($this->_currentPayRun === null) {
            if ($this->id === null) {
                return null;
            }

            if (($this->_currentPayRun = Staff::$plugin->payRuns->getLastPayRunByEmployer($this->id)) === null) {
                // The author is probably soft-deleted. Just no author is set
                $this->_currentPayRun = null;
            }
        }

        return $this->_currentPayRun ?: null;
    }

    /**
     * Returns the the default pay options.
     *
     * @return PayOption|null
     */
    public function getDefaultPayOptions(): ?PayOption
    {
        if ($this->_defaultPayOptions === null) {

            if (($this->_defaultPayOptions = Staff::$plugin->payOptions->getPayOptionsByEmployer($this->id)) === null) {
                // The author is probably soft-deleted. Just no author is set
                $this->_defaultPayOptions = null;
            }
        }

        return $this->_defaultPayOptions ?: null;
    }

    /**
     * Returns the hmrc details.
     *
     * @return array|null
     */
    public function getHmrcDetails(): ?array
    {
        if ($this->_hmrcDetails === null) {

            if (($this->_hmrcDetails = Staff::$plugin->employers->getHmrcDetailsByEmployer($this->id)) === null) {
                // The author is probably soft-deleted. Just no author is set
                $this->_hmrcDetails = null;
            }
        }

        return $this->_hmrcDetails ?: null;
    }

    /**
     * Returns the address.
     *
     * @return Address|null
     */
    public function getAddress(): ?array
    {
        if ($this->_address === null) {

            if (($this->_address = Staff::$plugin->employers->getAddressByEmployer($this->id)) === null) {
                // The author is probably soft-deleted. Just no author is set
                $this->_address = null;
            }
        }

        return $this->_address ?: null;
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

    /**
     * @return string
     */
    public function getCrn(): string
    {
        return SecurityHelper::decrypt($this->crn);
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
    public function afterSave(bool $isNew): void
    {
        if (!$this->propagating) {
            $this->_saveRecord($isNew);
        }

        parent::afterSave($isNew);
    }

    /**
     * Saved the employer record
     * @param bool $isNew
     */
    private function _saveRecord(bool $isNew): void
    {
        $logger = new Logger();

        try {
            if (!$isNew) {
                $record = EmployerRecord::findOne($this->id);

                if (!$record) {
                    throw new Exception('Invalid employer ID: ' . $this->id);
                }
            } else {
                $record = new EmployerRecord();
                $record->id = (int)$this->id;
            }

            $record->slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $this->name ?? ''), '-'));
            $record->staffologyId = $this->staffologyId;
            $record->name = $this->name ?? '';
            $record->crn = SecurityHelper::encrypt($this->crn ?? '');
            $record->logoUrl = $this->logoUrl ?? '';
            $record->startYear = $this->startYear ?? null;
            $record->currentYear = $this->currentYear ?? null;
            $record->employeeCount = $this->employeeCount ?? null;

            $success = $record->save(false);

            if (!$success) {
                $errors = "";

                foreach ($record->errors as $err) {
                    $errors .= implode(',', $err);
                }

                $logger->stdout($errors . PHP_EOL, $logger::FG_RED);
                Craft::error($record->errors, __METHOD__);
            }
        } catch (\Exception $e) {
            $logger->stdout(PHP_EOL, $logger::RESET);
            $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
            Craft::error($e->getMessage(), __METHOD__);
        }
    }
}
