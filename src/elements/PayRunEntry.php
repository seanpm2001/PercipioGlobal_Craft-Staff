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

use percipiolondon\staff\elements\db\PayRunEntryQuery;
use percipiolondon\staff\helpers\Logger;
use percipiolondon\staff\records\PayRunEntry as PayRunEntryRecord;

use percipiolondon\staff\Staff;
use yii\base\InvalidConfigException;
use yii\db\Exception;

/**
 * PayRunEntry Element
 *
 * Element is the base class for classes representing elements in terms of objects.
 *
 */
class PayRunEntry extends Element
{
    // Public Properties
    // =========================================================================

    public ?string $staffologyId = null;
    public ?int $payRunId = null;
    public ?int $employerId = null;
    public ?int $employeeId = null;
    public ?string $taxYear = null;
    public ?string $startDate = null;
    public ?string $endDate = null;
    public ?string $note = null;
    public ?string $bacsSubReference = null;
    public ?string $bacsHashcode = null;
    public ?string $percentageOfWorkingDaysPaidAsNormal = null;
    public ?string $workingDaysNotPaidAsNormal = null;
    public ?string $payPeriod = null;
    public ?string $ordinal = null;
    public ?string $period = null;
    public ?string $isNewStarter = null;
    public ?string $unpaidAbsence = null;
    public ?string $hasAttachmentOrders = null;
    public ?string $paymentDate = null;
    public ?string $periodOverrides = null;
    public ?string $forcedCisVatAmount = null;
    public ?string $holidayAccrued = null;
    public ?string $state = null;
    public ?string $isClosed = null;
    public ?string $manualNi = null;
    public ?string $payrollCodeChanged = null;
    public ?string $aeNotEnroledWarning = null;
    public ?string $receivingOffsetPay = null;
    public ?string $paymentAfterLearning = null;
    public ?string $pdf = null;

    private ?array $_totals;
    private string|array|null $_employee;
    private string|array|null $_pensionSummary;

    // Static Methods
    // =========================================================================

    /**
     * Returns the display name of this class.
     *
     * @return string The display name of this class.
     */
    public static function displayName(): string
    {
        return Craft::t('staff-management', 'PayRunEntry');
    }

    /**
     * @inheritdoc
     */
    public static function lowerDisplayName(): string
    {
        return Craft::t('staff-management', 'payrunentry');
    }

    /**
     * @inheritdoc
     */
    public static function pluralDisplayName(): string
    {
        return Craft::t('staff-management', 'PayRunEntries');
    }

    /**
     * @inheritdoc
     */
    public static function pluralLowerDisplayName(): string
    {
        return Craft::t('staff-management', 'payrunentries');
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
        return new PayRunEntryQuery(static::class);
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
        $ids = self::_getPayrunEntryIds();

        return [
            [
                'key' => '*',
                'label' => 'All payrun entries',
                'defaultSort' => ['id', 'desc'],
                'criteria' => ['id' => $ids],
            ],
        ];
    }

    // Public Methods
    // =========================================================================
    /**
     * Returns the payrun totals.
     *
     * @return array|null
     * @throws InvalidConfigException if [[totalId]] is set but invalid
     */
    public function getTotals(): ?array
    {

        if ($this->_totals === null) {

            if (($this->_totals = Staff::$plugin->totals->getTotalsByPayRunEntry($this->id)) === null) {
                $this->_totals = null;
            }
        }

        return $this->_totals ?: null;
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
     * Returns the employer
     *
     * @return string|null
     * @throws InvalidConfigException if [[employerId]] is set but invalid
     */
    public function getPensionSummary(): ?array
    {
        if ($this->_pensionSummary === null) {

            if (($this->_pensionSummary = Staff::$plugin->pensions->getPensionSummaryByPayRunEntryId($this->id)) === null) {
                // The author is probably soft-deleted. Just no author is set
                $this->_pensionSummary = null;
            }
        }

        return $this->_pensionSummary ?: null;
    }


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
        return 'PayRunEntry';
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

    /**
     * Returns all payrunEntry ID's
     *
     * @return array
     */
    private static function _getPayrunEntryIds(): array
    {
        $payrunEntryIds = [];

        $payrunentries = (new Query())
            ->from('{{%staff_pay_run_entries}}')
            ->select('id')
            ->all();

        foreach ($payrunentries as $payrunentry) {
            $payrunEntryIds[] = $payrunentry['id'];
        }

        return $payrunEntryIds;
    }

    private function _saveRecord($isNew)
    {
        $logger = new Logger();

        try {
            if (!$isNew) {
                $record = PayRunEntryRecord::findOne($this->id);

                if (!$record) {
                    throw new Exception('Invalid pay run entry ID: ' . $this->id);
                }
            } else {
                $record = new PayRunEntryRecord();
                $record->id = (int)$this->id;
            }

            $record->employerId = $this->employerId ?? null;
            $record->employeeId = $this->employeeId ?? null;
            $record->payRunId = $this->payRunId ?? null;
            $record->staffologyId = $this->staffologyId;
            $record->taxYear = $this->taxYear;
            $record->startDate = $this->startDate;
            $record->endDate = $this->endDate;
            $record->note = $this->note;
            $record->bacsSubReference = $this->bacsSubReference;
            $record->bacsHashcode = $this->bacsHashcode;
            $record->percentageOfWorkingDaysPaidAsNormal = $this->percentageOfWorkingDaysPaidAsNormal;
            $record->workingDaysNotPaidAsNormal = $this->workingDaysNotPaidAsNormal;
            $record->payPeriod = $this->payPeriod;
            $record->ordinal = $this->ordinal;
            $record->period = $this->period;
            $record->isNewStarter = $this->isNewStarter;
            $record->unpaidAbsence = $this->unpaidAbsence;
            $record->hasAttachmentOrders = $this->hasAttachmentOrders;
            $record->paymentDate = $this->paymentDate;
            $record->forcedCisVatAmount = $this->forcedCisVatAmount;
            $record->holidayAccrued = $this->holidayAccrued;
            $record->state = $this->state;
            $record->isClosed = $this->isClosed;
            $record->manualNi = $this->manualNi;
            $record->payrollCodeChanged = $this->payrollCodeChanged;
            $record->aeNotEnroledWarning = $this->aeNotEnroledWarning;
            $record->receivingOffsetPay = $this->receivingOffsetPay;
            $record->paymentAfterLearning = $this->paymentAfterLearning;
            $record->pdf = $this->pdf;

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
