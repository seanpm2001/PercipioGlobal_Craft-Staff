<?php

namespace percipiolondon\staff\elements;

use Craft;
use craft\base\Element;
use craft\elements\db\ElementQueryInterface;
use percipiolondon\staff\elements\db\HistoryQuery;
use percipiolondon\staff\records\Histories;

/**
 *
 * @property-read string $gqlTypeName
 */
class History extends Element
{
    // Static Properties
    // =========================================================================
    /**
     * @var array
     */
    public const TYPES = ['system', 'employee', 'payroll', 'pension', 'benefit'];

    // Public Properties
    // =========================================================================
    /**
     * @var int|null
     */
    public ?int $employerId = null;
    /**
     * @var int|null
     */
    public ?int $employeeId = null;
    /**
     * @var int|null
     */
    public ?int $administerId = null;
    /**
     * @var string|null
     */
    public ?string $message = null;
    /**
     * @var string|null
     */
    public ?string $data = null;
    /**
     * @var string|null
     */
    public ?string $type = null;


    // Static Methods
    // =========================================================================
    /**
     * @return string
     */
    public static function displayName(): string
    {
        return Craft::t('staff-management', 'History');
    }

    /**
     * @return string
     */
    public static function lowerDisplayName(): string
    {
        return Craft::t('staff-management', 'history');
    }

    /**
     * @return string
     */
    public static function pluralDisplayName(): string
    {
        return Craft::t('staff-management', 'Histories');
    }

    /**
     * @return string
     */
    public static function pluralLowerDisplayName(): string
    {
        return Craft::t('staff-management', 'histories');
    }

    /**
     * @return ElementQueryInterface
     */
    public static function find(): ElementQueryInterface
    {
        return new HistoryQuery(static::class);
    }
    /**
     * @param mixed $context
     * @return string
     */
    public static function gqlTypeNameByContext($context): string
    {
        return 'History';
    }


    // Public Methods
    // =========================================================================
    /**
     * @return array
     */
    public function defineRules(): array
    {
        $rules = parent::defineRules();
        $rules[] = [['employerId', 'employeeId', 'type', 'message'], 'required'];
        $rules[] = ['type', function($attribute, $params) {
            if (!in_array($this->$attribute, self::TYPES)) {
                $this->addError($attribute, "$attribute is not a valid type");
            }
        }];

        return $rules;
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

    /**
     * @param bool $isNew
     */
    public function afterSave(bool $isNew): void
    {
        $this->_saveRecord($isNew);

        parent::afterSave($isNew);
    }


    // Private Methods
    // =========================================================================
    /**
     * @param bool $isNew
     */
    private function _saveRecord(bool $isNew): void
    {
        try {
            if (!$isNew) {
                $history = Histories::findOne($this->id);

                if ($history) {
                    throw new \Exception('Invalid request ID: ' . $this->id);
                }
            } else {
                $history = new Histories();
                $history->id = $this->id;
            }

            $history->message = $this->message;
            $history->employerId = $this->employerId;
            $history->employeeId = $this->employeeId;
            $history->data = $this->data;
            $history->administerId = $this->administerId;
            $history->type = $this->type;

            $history->save();

        } catch (\Exception $e) {
            Craft::error($e->getMessage(), __METHOD__);
        }
    }
}