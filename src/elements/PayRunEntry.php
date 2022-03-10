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

use craft\validators\DateTimeValidator;
use percipiolondon\staff\helpers\Logger;
use percipiolondon\staff\records\Employee;
use percipiolondon\staff\records\Employer;
use percipiolondon\staff\Staff;

use Craft;
use craft\base\Element;
use craft\elements\db\ElementQuery;
use craft\elements\db\ElementQueryInterface;
use percipiolondon\staff\elements\db\PayRunEntryQuery;
use percipiolondon\staff\records\PayRunEntry as PayRunEntryRecord;
use yii\db\Exception;

/**
 * PayRunEntry Element
 *
 * Element is the base class for classes representing elements in terms of objects.
 *
 * @property FieldLayout|null      $fieldLayout           The field layout used by this element
 * @property array                 $htmlAttributes        Any attributes that should be included in the element’s DOM representation in the Control Panel
 * @property int[]                 $supportedSiteIds      The site IDs this element is available in
 * @property string|null           $uriFormat             The URI format used to generate this element’s URL
 * @property string|null           $url                   The element’s full URL
 * @property \Twig_Markup|null     $link                  An anchor pre-filled with this element’s URL and title
 * @property string|null           $ref                   The reference string to this element
 * @property string                $indexHtml             The element index HTML
 * @property bool                  $isEditable            Whether the current user can edit the element
 * @property string|null           $cpEditUrl             The element’s CP edit URL
 * @property string|null           $thumbUrl              The URL to the element’s thumbnail, if there is one
 * @property string|null           $iconUrl               The URL to the element’s icon image, if there is one
 * @property string|null           $status                The element’s status
 * @property Element               $next                  The next element relative to this one, from a given set of criteria
 * @property Element               $prev                  The previous element relative to this one, from a given set of criteria
 * @property Element               $parent                The element’s parent
 * @property mixed                 $route                 The route that should be used when the element’s URI is requested
 * @property int|null              $structureId           The ID of the structure that the element is associated with, if any
 * @property ElementQueryInterface $ancestors             The element’s ancestors
 * @property ElementQueryInterface $descendants           The element’s descendants
 * @property ElementQueryInterface $children              The element’s children
 * @property ElementQueryInterface $siblings              All of the element’s siblings
 * @property Element               $prevSibling           The element’s previous sibling
 * @property Element               $nextSibling           The element’s next sibling
 * @property bool                  $hasDescendants        Whether the element has descendants
 * @property int                   $totalDescendants      The total number of descendants that the element has
 * @property string                $title                 The element’s title
 * @property string|null           $serializedFieldValues Array of the element’s serialized custom field values, indexed by their handles
 * @property array                 $fieldParamNamespace   The namespace used by custom field params on the request
 * @property string                $contentTable          The name of the table this element’s content is stored in
 * @property string                $fieldColumnPrefix     The field column prefix this element’s content uses
 * @property string                $fieldContext          The field context this element’s content uses
 *
 * http://pixelandtonic.com/blog/craft-element-types
 *
 * @author    Percipio
 * @package   Staff
 * @since     1.0.0-alpha.1
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
     * Returns whether elements of this type will be storing any data in the `content`
     * table (tiles or custom fields).
     *
     * @return bool Whether elements of this type will be storing any data in the `content` table.
     */
    public static function hasContent(): bool
    {
        return false;
    }

    /**
     * Returns whether elements of this type have traditional titles.
     *
     * @return bool Whether elements of this type have traditional titles.
     */
    public static function hasTitles(): bool
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public static function hasUris(): bool
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public static function hasStatuses(): bool
    {
        return false;
    }

    /**
     * Returns whether elements of this type have statuses.
     *
     * If this returns `true`, the element index template will show a Status menu
     * by default, and your elements will get status indicator icons next to them.
     *
     * Use [[statuses()]] to customize which statuses the elements might have.
     *
     * @return bool Whether elements of this type have statuses.
     * @see statuses()
     */
    public static function isLocalized(): bool
    {
        return true;
    }

    /**
     * Creates an [[ElementQueryInterface]] instance for query purpose.
     *
     * The returned [[ElementQueryInterface]] instance can be further customized by calling
     * methods defined in [[ElementQueryInterface]] before `one()` or `all()` is called to return
     * populated [[ElementInterface]] instances. For example,
     *
     * ```php
     * // Find the entry whose ID is 5
     * $entry = Entry::find()->id(5)->one();
     *
     * // Find all assets and order them by their filename:
     * $assets = Asset::find()
     *     ->orderBy('filename')
     *     ->all();
     * ```
     *
     * If you want to define custom criteria parameters for your elements, you can do so by overriding
     * this method and returning a custom query class. For example,
     *
     * ```php
     * class Product extends Element
     * {
     *     public static function find()
     *     {
     *         // use ProductQuery instead of the default ElementQuery
     *         return new ProductQuery(get_called_class());
     *     }
     * }
     * ```
     *
     * You can also set default criteria parameters on the ElementQuery if you don’t have a need for
     * a custom query class. For example,
     *
     * ```php
     * class Customer extends ActiveRecord
     * {
     *     public static function find()
     *     {
     *         return parent::find()->limit(50);
     *     }
     * }
     * ```
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
                'label' => 'All payruns entries',
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
     * Returns whether the current user can edit the element.
     *
     * @return bool
     */
    public function getIsEditable(): bool
    {
        return true;
    }

    /**
     * @return string
     */
    public function getCpEditUrl()
    {
        return 'staff-management/payrunentries/' . $this->id;
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

    public function getGroup()
    {
        if ($this->groupId === null) {
            throw new InvalidConfigException('Tag is missing its group ID');
        }

        if (($group = Craft::$app->getTags()->getTagGroupById($this->groupId)) === null) {
            throw new InvalidConfigException('Invalid tag group ID: '.$this->groupId);
        }

        return $group;
    }

    // Indexes, etc.
    // -------------------------------------------------------------------------

    /**
     * Returns the HTML for the element’s editor HUD.
     *
     * @return string The HTML for the editor HUD
     */
    public function getEditorHtml(): string
    {
        $html = Craft::$app->getView()->renderTemplateMacro('_includes/forms', 'textField', [
            [
                'label' => Craft::t('app', 'Title'),
                'siteId' => $this->siteId,
                'id' => 'title',
                'name' => 'title',
                'value' => $this->title,
                'errors' => $this->getErrors('title'),
                'first' => true,
                'autofocus' => true,
                'required' => true
            ]
        ]);

        $html .= parent::getEditorHtml();

        return $html;
    }

    /**
     * @inheritdoc
     * @since 3.3.0
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
     * @since 3.3.0
     */
    public function getGqlTypeName(): string
    {
        return static::gqlTypeNameByContext($this);
    }

    // Events
    // -------------------------------------------------------------------------

    /**
     * Performs actions before an element is saved.
     *
     * @param bool $isNew Whether the element is brand new
     *
     * @return bool Whether the element should be saved
     */
    public function beforeSave(bool $isNew): bool
    {
        return true;
    }

    /**
     * Performs actions after an element is saved.
     *
     * @param bool $isNew Whether the element is brand new
     *
     * @return void
     */
    public function afterSave(bool $isNew)
    {
//        if (!$this->propagating) {
//
//            $this->_saveRecord($isNew);
//        }

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
    }

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

                //foreign keys
                $totalsId = $record->totalsId;
                $totalsYtdId = $record->totalsYtdId;
                $payOptionsId = $record->payOptionsId;

            } else {
                $record = new PayRunEntryRecord();
                $record->id = (int)$this->id;

                //foreign keys
                $totalsId = null;
                $totalsYtdId = null;
                $payOptionsId = null;
            }

            //foreign keys
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

            $success = $record->save(false);

        } catch (\Exception $e) {

            $logger = new Logger();
            $logger->stdout(PHP_EOL, $logger::RESET);
            $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
            Craft::error($e->getMessage(), __METHOD__);
        }
    }
}
