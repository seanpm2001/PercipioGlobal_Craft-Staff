<?php
/**
 * staff-management plugin for Craft CMS 3.x
 *
 * Craft Staff Management provides an HR solution for payroll and benefits
 *
 * @link      http://percipio.london
 * @copyright Copyright (c) 2021 Percipio
 */

namespace percipiolondon\craftstaff\elements;

use craft\validators\DateTimeValidator;
use percipiolondon\craftstaff\Craftstaff;

use Craft;
use craft\base\Element;
use craft\elements\db\ElementQuery;
use craft\elements\db\ElementQueryInterface;
use percipiolondon\craftstaff\elements\db\PayRunEntryQuery;
use percipiolondon\craftstaff\records\PayRunEntry as PayRunEntryRecord;
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
 * @package   Craftstaff
 * @since     1.0.0-alpha.1
 */
class PayRunEntry extends Element
{
    // Public Properties
    // =========================================================================

    public $siteId;
    public $staffologyId;
    public $payRunId;
    public $employerId;
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
    public $holidayAccured;
    public $state;
    public $isClosed;
    public $manualNi;
    public $nationalInsuranceCalculation;
    public $payrollCodeChanged;
    public $aeNotEnrolledWarning;
    public $fps;
    public $recievingOffsetPay;
    public $paymentAfterLearning;
    public $umbrellaPayment;
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
        return Craft::t('staff-management', 'payrunentry');
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
        return true;
    }

    /**
     * @inheritdoc
     */
    public static function hasUris(): bool
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
        $sources = [];

        return $sources;
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
        return [];
//        return [
//            [[
//                'siteId',
//                'payRunId',
//                'employerId',
//                'ordinal',
//                'period',
//            ], 'number', 'integerOnly' => true], [[
//                'percentageOfWorkingDaysPaidAsNormal',
//                'workingDaysNotPaidAsNormal',
//                'forcedCisVatAmount',
//                'holidayAccured',
//            ], 'double'],
//            [['startDate', 'endDate', 'paymentDate'], DateTimeValidator::class],
////            ['state', 'exists', 'targetAttribute' => ['Open', 'SubmittedForProcessing', 'Processing', 'AwaitingApproval', 'Approved', 'Finalised']],
//            [[
//                'staffologyId',
//                'taxYear',
//                'note',
//                'bacsSubReference',
//                'bacsHashcode',
//                'payPeriod',
//                'priorPayrollCode',
//                'payOptions',
//                'pensionSummary',
//                'totals',
//                'periodOverrides',
//                'totalsYtd',
//                'totalsYtdOverrides',
//                'state',
//                'nationalInsuranceCalculation',
//                'fps',
//                'umbrellaPayment',
//                'pdf',
//            ], 'string'],
//            [[
//                'unpaidAbsence',
//                'unpaidAbsence',
//                'hasAttachmentOrders',
//                'isClosed',
//                'manualNi',
//                'payrollCodeChanged',
//                'aeNotEnrolledWarning',
//                'recievingOffsetPay',
//                'paymentAfterLearning',
//            ], 'boolean'],
//        ];
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
     * Returns the field layout used by this element.
     *
     * @return FieldLayout|null
     */
    public function getFieldLayout()
    {
        $tagGroup = $this->getGroup();

        if ($tagGroup) {
            return $tagGroup->getFieldLayout();
        }

        return null;
    }

    public function getGroup()
    {
//        if ($this->groupId === null) {
//            throw new InvalidConfigException('Tag is missing its group ID');
//        }
//
//        if (($group = Craft::$app->getTags()->getTagGroupById($this->groupId)) === null) {
//            throw new InvalidConfigException('Invalid tag group ID: '.$this->groupId);
//        }
//
//        return $group;
        return null;
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
    }

    /**
     * Performs actions before an element is moved within a structure.
     *
     * @param int $structureId The structure ID
     *
     * @return bool Whether the element should be moved within the structure
     */
    public function beforeMoveInStructure(int $structureId): bool
    {
        return true;
    }

    /**
     * Performs actions after an element is moved within a structure.
     *
     * @param int $structureId The structure ID
     *
     * @return void
     */
    public function afterMoveInStructure(int $structureId)
    {
    }

    private function _saveRecord($isNew)
    {
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

            $record->siteId = $this->siteId;
            $record->employerId = $this->employerId;
            $record->payRunId = $this->payRunId;
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
            $record->priorPayrollCode = $this->priorPayrollCode;
            $record->payOptions = $this->payOptions;
            $record->pensionSummary = $this->pensionSummary;
            $record->totals = $this->totals;
            $record->periodOverrides = $this->periodOverrides;
            $record->totalsYtd = $this->totalsYtd;
            $record->totalsYtdOverrides = $this->totalsYtdOverrides;
            $record->forcedCisVatAmount = $this->forcedCisVatAmount;
            $record->holidayAccured = $this->holidayAccured;
            $record->state = $this->state;
            $record->isClosed = $this->isClosed;
            $record->manualNi = $this->manualNi;
            $record->nationalInsuranceCalculation = $this->nationalInsuranceCalculation;
            $record->aeNotEnrolledWarning = $this->aeNotEnrolledWarning;
            $record->fps = $this->fps;
            $record->recievingOffsetPay = $this->recievingOffsetPay;
            $record->paymentAfterLearning = $this->paymentAfterLearning;
            $record->umbrellaPayment = $this->umbrellaPayment;

            $success = $record->save(false);

        } catch (\Exception $e) {

            echo "---- error -----\n";
            var_dump($e->getMessage());
            Craft::error($e->getMessage(), __METHOD__);
            echo "\n---- end error ----";
        }
    }
}
