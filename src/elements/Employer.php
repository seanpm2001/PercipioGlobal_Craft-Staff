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

use percipiolondon\staff\records\Employer as EmployerRecord;
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

    public $slug;
    public $siteId;
    public $staffologyId;
    public $name;
    public $logoUrl;
    public $crn;
    public $defaultPayOptionsId;
    public $address;
    public $addressId;
    public $startYear;
    public $currentYear;
    public $employeeCount;

    private $_currentPayRun;

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
     * Defines the sources that elements of this type may belong to.
     *
     * @param string|null $context The context ('index' or 'modal').
     *
     * @return array The sources.
     * @see sources()
     */
    protected static function defineSources(string $context = null): array
    {
        $ids = self::_getEmployerIds();

        return [
            [
                'key' => '*',
                'label' => 'All Employers',
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
     * @return PayRun|null
     */
    public function getCurrentPayRun()
    {
        if ($this->_currentPayRun === null) {
            if ($this->id === null) {
                return null;
            }

            if (($this->_currentPayRun = Staff::$plugin->payRuns->getLastPayRunByEmployer($this->id)) === null) {
                // The author is probably soft-deleted. Just no author is set
                $this->_currentPayRun = false;
            }
        }

        return $this->_currentPayRun ?: null;
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
        return 'Employer';
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
        if (!$this->propagating) {
            $this->_saveRecord($isNew);
        }

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

            $record->slug = SecurityHelper::encrypt((strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $this->name ?? ''), '-'))));
            $record->staffologyId = $this->staffologyId;
            $record->name = SecurityHelper::encrypt($this->name ?? '');
            $record->crn = SecurityHelper::encrypt($this->crn ?? '');
            $record->logoUrl = SecurityHelper::encrypt($this->logoUrl ?? '');
            $record->addressId = $this->addressId ?? null;
            $record->startYear = $this->startYear ?? null;
            $record->currentYear = $this->currentYear ?? null;
            $record->employeeCount = $this->employeeCount ?? null;
            $record->defaultPayOptionsId = $this->defaultPayOptionsId ?? null;

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

    /**
     * Returns all employer ID's
     *
     * @return array
     */
    private static function _getEmployerIds(): array
    {
        $employerIds = [];

        $employers = (new Query())
            ->from('{{%staff_employers}}')
            ->select('id')
            ->all();

        foreach ($employers as $employer) {
            $employerIds[] = $employer['id'];
        }

        return $employerIds;
    }
}
