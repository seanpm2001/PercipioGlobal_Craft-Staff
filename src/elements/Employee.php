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

use craft\db\Query;

use Craft;
use craft\base\Element;
use craft\elements\db\ElementQuery;
use craft\elements\db\ElementQueryInterface;
use craft\elements\User;

use percipiolondon\staff\elements\db\EmployeeQuery;

use yii\db\Exception;

/**
 * Employee Element
 */

class Employee extends Element
{
    // Public Properties
    // =========================================================================

    public $staffologyId;
    public $employerId;
    public $userId;
    public $personalDetails;
    public $employmentDetails;
    public $autoEnrolment;
    public $leaveSettings;
    public $rightToWork;
    public $bankDetails;
    public $status;
    public $aeNotEnroledWarning;
    public $niNumber;
    public $sourceSystemId;
    public $isDirector;

    // Static Methods
    // =========================================================================

    /**
     * Returns the display name of this class.
     *
     * @return string The display name of this class.
     */
    public static function displayName(): string
    {
        return Craft::t('staff-management', 'Employee');
    }

    /**
     * @inheritdoc
     */
    public static function lowerDisplayName(): string
    {
        return Craft::t('staff-management', 'employee');
    }

    /**
     * @inheritdoc
     */
    public static function pluralDisplayName(): string
    {
        return Craft::t('staff-management', 'Employees');
    }

    /**
     * @inheritdoc
     */
    public static function pluralLowerDisplayName(): string
    {
        return Craft::t('staff-management', 'employees');
    }

    /**
     * @inheritdoc
     */
    public static function refHandle()
    {
        return 'employee';
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
        return new EmployeeQuery(static::class);
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
        $ids = self::_getEmployeeIds();

        return [
            [
                'key' => '*',
                'label' => 'All Employees',
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
     * @since 3.3.0
     */
    public static function gqlScopesByContext($context): array
    {
        return ['employees.' . $context->uid];
    }

    public static function gqlTypeNameByContext($context): string
    {
        return 'Employee';
    }

    /**
     * @inheritdoc
     * @since 3.3.0
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
     * Returns all employee ID's
     *
     * @return array
     */
    private static function _getEmployeeIds(): array
    {
        $employeeIds = [];

        $employees = (new Query())
            ->from('{{%staff_employees}}')
            ->select('id')
            ->all();

        foreach ($employees as $employee) {
            $employeeIds[] = $employee['id'];
        }

        return $employeeIds;
    }
}
