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
use craft\elements\db\ElementQuery;
use craft\elements\db\ElementQueryInterface;
use craft\validators\DateTimeValidator;

use percipiolondon\staff\elements\db\PayRunEntryQuery;
use percipiolondon\staff\helpers\Logger;
use percipiolondon\staff\records\Employee;
use percipiolondon\staff\records\Employer;
use percipiolondon\staff\records\PayRunEntry as PayRunEntryRecord;
use percipiolondon\staff\Staff;

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

    public $staffologyId;
    public $payRunId;
    public $employerId;
    public $employeeId;
    public $taxYear;
    public $startDate;
    public $endDate;
    public $note;
    public $bacsSubReference;
    public $bacsHashcode;
    public $percentageOfWorkingDaysPaidAsNormal;
    public $workingDaysNotPaidAsNormal;
    public $payPeriod;
    public $ordinal;
    public $period;
    public $isNewStarter;
    public $unpaidAbsence;
    public $hasAttachmentOrders;
    public $paymentDate;
    public $priorPayrollCode;
    public $payOptions;
    public $pensionSummary;
    public $totals;
    public $periodOverrides;
    public $totalsYtd;
    public $totalsYtdOverrides;
    public $forcedCisVatAmount;
    public $holidayAccrued;
    public $state;
    public $isClosed;
    public $manualNi;
    public $nationalInsuranceCalculation;
    public $payrollCodeChanged;
    public $aeNotEnroledWarning;
    public $fps;
    public $receivingOffsetPay;
    public $paymentAfterLearning;
    public $umbrellaPayment;
    public $employee;
    public $pdf;

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
     * @inheritdoc
     */
    public static function refHandle()
    {
        return 'payRunEntry';
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

    /**
     * @inheritdoc
     * @since 1.0.0
     */
    public static function gqlScopesByContext($context): array
    {
        return ['payrunentries.' . $context->uid];
    }

    public static function gqlTypeNameByContext($context): string
    {
        return 'PayRunEntry';
    }

    /**
     * @inheritdoc
     * @since 1.0.0
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
            ->from('{{%staff_payrunentries}}')
            ->select('id')
            ->all();

        foreach ($payrunentries as $payrunentry) {
            $payrunEntryIds[] = $payrunentry['id'];
        }

        return $payrunEntryIds;
    }

    private function _saveRecord($isNew)
    {
        try {
            if (!$isNew) {
                $record = PayRunEntryRecord::findOne($this->id);

                if (!$record) {
                    throw new Exception('Invalid pay run entry ID: ' . $this->id);
                }

                // Foreign keys
                $totalsId = $record->totalsId;
                $totalsYtdId = $record->totalsYtdId;
                $payOptionsId = $record->payOptionsId;

            } else {
                $record = new PayRunEntryRecord();
                $record->id = (int)$this->id;

                // Foreign keys
                $totalsId = null;
                $totalsYtdId = null;
                $payOptionsId = null;
            }

            // Foreign keys
            $totals = Staff::$plugin->payRuns->saveTotals($this->totals, $totalsId);
            $totalsYtd = Staff::$plugin->payRuns->saveTotals($this->totalsYtd, $totalsYtdId);
            $payOptions = Staff::$plugin->payRuns->savePayOptions($this->payOptions, $payOptionsId);
            $employee = Employee::findOne(['staffologyId' => $this->employeeId]);
            $employer = Employer::findOne(['staffologyId' => $this->employerId]);

            $record->employerId = $employer->id ?? null;
            $record->employeeId = $employee->id ?? null;
            $record->payRunId = $this->payRunId ?? null;
            $record->payOptionsId = $payOptions->id ?? null;
            $record->totalsId = $totals->id ?? null;
            $record->totalsYtdId = $totalsYtd->id ?? null;
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

            $record->save(false);

        } catch (\Exception $e) {

            $logger = new Logger();
            $logger->stdout(PHP_EOL, $logger::RESET);
            $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
            Craft::error($e->getMessage(), __METHOD__);
        }
    }
}
