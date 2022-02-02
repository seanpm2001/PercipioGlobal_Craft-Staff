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

use percipiolondon\staff\Staff;

use Craft;
use craft\elements\User;
use craft\base\Element;
use craft\elements\db\ElementQuery;
use craft\elements\db\ElementQueryInterface;
use percipiolondon\staff\elements\db\EmployeeQuery;
use percipiolondon\staff\records\Employee as EmployeeRecord;
use percipiolondon\staff\records\Permission;

/**
 * Employee Element
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
class Employee extends Element
{
    // Public Properties
    // =========================================================================

    public $siteId;
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
            self::STATUS_ENABLED => Craft::t('company-management', 'Enabled'),
            self::STATUS_DISABLED => Craft::t('company-management', 'Disabled'),
        ];
    }

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
        return 'staff-management/employees/' . $this->id;
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

    private function _saveRecord($isNew)
    {
        try {
            if (!$isNew) {
                $record = EmployeeRecord::findOne($this->id);

                if (!$record) {
                    throw new Exception('Invalid employee ID: ' . $this->id);
                }

            } else {
                $record = new EmployeeRecord();
                $record->id = (int)$this->id;
            }

            if($this->personalDetails && array_key_exists('email', $this->personalDetails)) {
                $user = User::findOne(['email' => $this->personalDetails['email']]);

                // check if user exists, if so, skip this step
                if(!$user) {

                    //create user
                    $user = new User();
                    $user->firstName = $this->personalDetails['firstName'];
                    $user->lastName = $this->personalDetails['lastName'];
                    $user->username = $this->personalDetails['email'];
                    $user->email = $this->personalDetails['email'];

                    $success = Craft::$app->elements->saveElement($user, true);

                    if(!$success){
                        throw new Exception("The user couldn't be created");
                    }

                    Craft::info("Craft Staff: new user creation: ".$user->id);

                    // assign user to group
                    $group = Craft::$app->getUserGroups()->getGroupByHandle('hardingUsers');
                    Craft::$app->getUsers()->assignUserToGroups($user->id, [$group->id]);
                }

                //assign the userId to the employee record
                $this->userId = $user->id;
            }

            $record->employerId = $this->employerId;
            $record->staffologyId = $this->staffologyId;
            $record->siteId = $this->siteId;
            $record->personalDetails = $this->personalDetails;
            $record->employmentDetails = $this->employmentDetails;
            $record->autoEnrolment = $this->autoEnrolment;
            $record->leaveSettings = $this->leaveSettings;
            $record->rightToWork = $this->rightToWork;
            $record->bankDetails = $this->bankDetails;
            $record->status = $this->status;
            $record->aeNotEnroledWarning = $this->aeNotEnroledWarning;
            $record->sourceSystemId = $this->sourceSystemId;
            $record->niNumber = $this->niNumber;
            $record->userId = $this->userId;
            $record->isDirector = $this->isDirector;

            $success = $record->save(false);

            if($isNew) {
                //assign permissions to employee
                if($this->isDirector) {
                    $permissions = Permission::find()->all();
                } else {
                    $permissions = [Permission::findOne(['name' => 'access:employer'])];
                }

                Staff::$plugin->userPermissions->createPermissions($permissions, $this->userId, $this->id);
            }

        } catch (\Exception $e) {

            echo "---- error -----\n";
            var_dump($e->getMessage());
            Craft::error($e->getMessage(), __METHOD__);
            echo "\n---- end error ----";
        }

    }
}
