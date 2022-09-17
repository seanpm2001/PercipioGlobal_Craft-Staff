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

use percipiolondon\staff\elements\db\PayRunQuery;
use percipiolondon\staff\helpers\Logger;
use percipiolondon\staff\records\PayRun as PayRunRecord;

use percipiolondon\staff\Staff;
use yii\base\InvalidConfigException;
use yii\db\Exception;

/**
 * PayRun Element
 *
 * Element is the base class for classes representing elements in terms of objects.
 *
 *
 * @property-read string $gqlTypeName
 * @property-read null|string $employer
 * @property-read null|array $totals
 */
class PayRun extends Element
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
    public ?string $taxYear = null;
    /**
     * @var string|null
     */
    public ?string $taxMonth = null;
    /**
     * @var string|null
     */
    public ?string $payPeriod = null;
    /**
     * @var string|null
     */
    public ?string $ordinal = null;
    /**
     * @var string|null
     */
    public ?string $period = null;
    /**
     * @var string|null
     */
    public ?string $startDate = null;
    /**
     * @var string|null
     */
    public ?string $endDate = null;
    /**
     * @var string|null
     */
    public ?string $paymentDate = null;
    /**
     * @var string|null
     */
    public ?string $employeeCount = null;
    /**
     * @var string|null
     */
    public ?string $subContractorCount = null;
    /**
     * @var string|null
     */
    public ?string $state = null;
    /**
     * @var string|null
     */
    public ?string $dateClosed = null;
    /**
     * @var string|null
     */
    public ?string $pdf = null;
    /**
     * @var string|null
     */
    public ?string $url = null;
    /**
     * @var bool|null
     */
    public ?bool $isClosed = null;
    /**
     * @var int|null
     */
    public ?int $employerId = null;

    // Private Properties
    // =========================================================================
    /**
     * @var array|null
     */
    private ?array $_totals = null;
    /**
     * @var string|null
     */
    private ?string $_employer = null;


    // Static Methods
    // =========================================================================
    /**
     * Returns the display name of this class.
     *
     * @return string The display name of this class.
     */
    public static function displayName(): string
    {
        return Craft::t('staff-management', 'PayRun');
    }

    /**
     * @inheritdoc
     */
    public static function lowerDisplayName(): string
    {
        return Craft::t('staff-management', 'payruns');
    }

    /**
     * @inheritdoc
     */
    public static function pluralDisplayName(): string
    {
        return Craft::t('staff-management', 'PayRuns');
    }

    /**
     * @inheritdoc
     */
    public static function pluralLowerDisplayName(): string
    {
        return Craft::t('staff-management', 'payruns');
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
        return new PayRunQuery(static::class);
    }

    /**
     * @param mixed $context
     * @return string
     */
    public static function gqlTypeNameByContext($context): string
    {
        return 'PayRun';
    }

    // Public Methods
    // =========================================================================
    /**
     * Returns the payrun totals.
     *
     * @return array|null
     * @throws InvalidConfigException if [[totalId]] is set but invalid
     */
    public function getTotals()
    {
        if ($this->_totals === null) {

            if (($this->_totals = Staff::$plugin->totals->getTotalsByPayRun($this->id)) === null) {
                // The author is probably soft-deleted. Just no author is set
                $this->_totals = null;
            }
        }

        return $this->_totals ?: null;
    }

    /**
     * Returns the employer
     *
     * @return string|null
     */
    public function getEmployer(): string|null
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
     */
    public function afterSave(bool $isNew): void
    {
        $this->_saveRecord($isNew);

        parent::afterSave($isNew);
    }


    // Private Methods
    // -------------------------------------------------------------------------
    /**
     * @param $isNew
     */
    private function _saveRecord($isNew)
    {
        $logger = new Logger();

        try {
            if (!$isNew) {
                $record = PayRunRecord::findOne($this->id);

                if (!$record) {
                    throw new Exception('Invalid pay run ID: ' . $this->id);
                }
            } else {
                $record = new PayRunRecord();
                $record->id = (int)$this->id;
            }

            $record->employerId = $this->employerId ?? null;
            $record->taxYear = $this->taxYear;
            $record->taxMonth = $this->taxMonth;
            $record->payPeriod = $this->payPeriod;
            $record->ordinal = $this->ordinal;
            $record->period = $this->period;
            $record->startDate = $this->startDate;
            $record->endDate = $this->endDate;
            $record->paymentDate = $this->paymentDate;
            $record->employeeCount = $this->employeeCount;
            $record->subContractorCount = $this->subContractorCount;
            $record->state = $this->state;
            $record->isClosed = $this->isClosed;
            $record->dateClosed = $this->dateClosed;
            $record->url = $this->url;

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
