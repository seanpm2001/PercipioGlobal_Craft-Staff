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

use percipiolondon\staff\helpers\Logger;
use percipiolondon\staff\records\Countries;
use percipiolondon\staff\Staff;

use Craft;
use craft\base\Element;
use craft\elements\db\ElementQuery;
use craft\elements\db\ElementQueryInterface;
use percipiolondon\staff\elements\db\EmployerQuery;
use yii\base\InvalidConfigException;
use yii\db\Exception;
use yii\db\Query;
use percipiolondon\staff\records\Employer as EmployerRecord;
use percipiolondon\staff\records\Address as AddressRecord;

/**
 * Employer Element
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
    public $address;
    public $startYear;
    public $currentYear;
    public $employeeCount;

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
     * @inheritdoc
     */
    public static function refHandle()
    {
        return 'employer';
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
     * @inheritdoc
     */
    public static function hasStatuses(): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getStatus()
    {
        $status = parent::getStatus();

        return $status;
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
    public static function statuses(): array
    {
        return [
            self::STATUS_ENABLED => Craft::t('staff-management', 'Enabled'),
            self::STATUS_DISABLED => Craft::t('staff-management', 'Disabled'),
        ];
    }

    /**
     * @return bool
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
        return 'staff-management/employers/' . $this->id;
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
            throw new InvalidConfigException('Invalid tag group ID: ' . $this->groupId);
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
        return ['employers.' . $context->uid];
    }

    public static function gqlTypeNameByContext($context): string
    {
        return 'Employer';
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
     * @return array
     */
    protected static function defineTableAttributes(): array
    {
        return [
            'id' => ['label' => Craft::t('staff-management', 'Id')],
            'dateCreated' => ['label' => Craft::t('staff-management', 'Date Created')],
        ];
    }

    /**
     * @param string $source
     * @return array
     */
    protected static function defineDefaultTableAttributes(string $source): array
    {
        $attributes = [];
        $attributes[] = 'id';
        $attributes[] = 'dateCreated';
        $attributes[] = 'dateUpdated';

        return $attributes;
    }

    /**
     * @param string $attribute
     * @return string
     * @throws InvalidConfigException
     */
    protected function tableAttributeHtml(string $attribute): string
    {
        switch ($attribute) {
            case 'id':
                // use this to customise returned values (add links / mailto's etc)
                // https://docs.craftcms.com/commerce/api/v3/craft-commerce-elements-traits-orderelementtrait.html#protected-methods
                return $this->id;
        }

        return parent::tableAttributeHtml($attribute);
    }

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

    private function _saveRecord($isNew)
    {
        try {
            if (!$isNew) {

                $record = EmployerRecord::findOne($this->id);
                $address = AddressRecord::findOne($this->id);

                if (!$record) {
                    throw new Exception('Invalid employer ID: ' . $this->id);
                }

            } else {
                $record = new EmployerRecord();
                $record->id = (int)$this->id;

                $address = new AddressRecord();
            }

            $address = $this->_saveAddress($address);

            $record->slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $this->name), '-'));
            $record->staffologyId = $this->staffologyId;
            $record->name = Craft::$app->getSecurity()->hashData($this->name);
            $record->crn = $this->crn;
            $record->addressId = $address->id ?? null;
            $record->startYear = $this->startYear;
            $record->currentYear = $this->currentYear;
            $record->employeeCount = $this->employeeCount;

            $success = $record->save(false);

            if($success) {
                $address->employerId = $this->id;
            }

        } catch (\Exception $e) {

            $logger = new Logger();
            $logger->stdout(PHP_EOL, $logger::RESET);
            $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
            Craft::error($e->getMessage(), __METHOD__);
        }

    }

    private function _saveAddress(AddressRecord $address): AddressRecord {

        $countryName = $this->address['country'] ?? 'England';

        $country = Countries::find()
            ->where(['name' => $countryName])
            ->one();

        $record = AddressRecord::find()
            ->where(['employerId' => $this->id])
            ->one();

        if($record){
            $address = $record;
        }

        $address->countryId = $country->id ?? null;
        $address->address1 = $this->address['line1'] ?? null;
        $address->address2 = $this->address['line2'] ?? null;
        $address->address3 = $this->address['line3'] ?? null;
        $address->address4 = $this->address['line4'] ?? null;
        $address->address5 = $this->address['line5'] ?? null;
        $address->zipCode = $this->address['postCode'] ?? null;

        $address->save();

        return $address;
    }
}
