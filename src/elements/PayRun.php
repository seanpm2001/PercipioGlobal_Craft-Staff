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

use percipiolondon\staff\Staff;
use yii\base\InvalidConfigException;
use yii\db\Exception;

use percipiolondon\staff\helpers\Logger;
use percipiolondon\staff\records\PayRun as PayRunRecord;
use percipiolondon\staff\elements\db\PayRunQuery;

/**
 * PayRun Element
 *
 * @property PayRunTotals|null $payRunTotals the payrun totals.
 * Element is the base class for classes representing elements in terms of objects.
 *
 */
class PayRun extends Element
{
    // Public Properties
    // =========================================================================

    public $staffologyId;
    public $taxYear;
    public $taxMonth;
    public $payPeriod;
    public $ordinal;
    public $period;
    public $startDate;
    public $endDate;
    public $paymentDate;
    public $employeeCount;
    public $subContractorCount;
    public $totalsId;
    public $state;
    public $isClosed;
    public $dateClosed;
    public $pdf;
    public $url;
    public $employerId;

    private $_totals;
    private $_employer;

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
     * Defines the sources that elements of this type may belong to.
     *
     * @param string|null $context The context ('index' or 'modal').
     *
     * @return array The sources.
     * @see sources()
     */
    protected static function defineSources(string $context = null): array
    {
        $ids = self::_getPayrunIds();

        return [
            [
                'key' => '*',
                'label' => 'All payruns',
                'defaultSort' => ['id', 'desc'],
                'criteria' => ['id' => $ids],
            ]
        ];
    }

    // Public Methods
    // =========================================================================

    /**
     * Returns the payrun totals.
     *
     * @return PayRunTotals|null
     * @throws InvalidConfigException if [[totalId]] is set but invalid
     */
    public function getTotals()
    {
        if ($this->_totals === null) {
            if ($this->totalsId === null) {
                return null;
            }

            if (($this->_totals = Staff::$plugin->payRuns->getTotalsById($this->totalsId)) === null) {
                // The author is probably soft-deleted. Just no author is set
                $this->_totals = false;
            }
        }

        return $this->_totals ?: null;
    }

    /**
     * Returns the payrun totals.
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
                $this->_employer = false;
            }
        }

        return $this->_employer ?: null;
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
        return 'PayRun';
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

    public function datetimeAttributes(): array
    {
        $attributes = parent::datetimeAttributes();
        $attributes[] = 'dateClosed';
        $attributes[] = 'paymentDate';
        $attributes[] = 'startDate';
        $attributes[] = 'endDate';
        return $attributes;
    }

    /**
     * Returns all payrun ID's
     *
     * @return array
     */

    private static function _getPayrunIds(): array
    {
        $payrunIds = [];

        $payruns = (new Query())
            ->from('{{%staff_payrun}}')
            ->select('id')
            ->all();

        foreach ($payruns as $payrun) {
            $payrunIds[] = $payrun['id'];
        }

        return $payrunIds;
    }

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
            $record->totalsId = $this->totalsId ?? null;

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

            if(!$success) {
                $errors = "";

                foreach($record->errors as $err) {
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