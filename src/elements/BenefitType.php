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

use DateTime;
use Craft;
use craft\base\Element;
use craft\db\Query;
use craft\elements\db\ElementQueryInterface;

use craft\helpers\App;
use percipiolondon\staff\db\Table;
use percipiolondon\staff\elements\db\BenefitProviderQuery as BenefitProviderQuery;
use percipiolondon\staff\helpers\BenefitTypes;
use percipiolondon\staff\helpers\Logger;
use percipiolondon\staff\records\BenefitProvider as ProviderRecord;

/**
 * Employee Element
 */

class BenefitType extends Element
{
    // Public Properties
    // =========================================================================
    /**
     * @var int|null
     */
    public int|null $providerId = null;
    /**
     * @var string|null
     */
    public string|null $internalCode;
    /**
     * @var string|null
     */
    public string|null $status;
    /**
     * @var string|null
     */
    public string|null $policyName;
    /**
     * @var string|null
     */
    public string|null $policyNumber;
    /**
     * @var string|null
     */
    public string|null $policyHolder;
    /**
     * @var string|null
     */
    public string|null $content;
    /**
     * @var DateTime|null
     */
    public DateTime|null $policyStartDate;
    /**
     * @var DateTime|null
     */
    public DateTime|null $policyRenewalDate;
    /**
     * @var string|null
     */
    public string|null $paymentFrequency;
    /**
     * @var float|null
     */
    public float|null $commissionRate;

    /**
     * @var string|null
     */
    public string|null $benefitType;
    /**
     * @var array|null
     */
    public array|null $benefitTypeDental;
    /**
     * @var array|null
     */
    public array|null $benefitTypeGroupCriticalIllnessCover;
    /**
     * @var array|null
     */
    public array|null $benefitTypeGroupDeathInService;
    /**
     * @var array|null
     */
    public array|null $benefitTypeGroupIncomeProtection;
    /**
     * @var array|null
     */
    public array|null $benefitTypeGroupLifeAssurance;
    /**
     * @var array|null
     */
    public array|null $benefitTypeHealthCashPlan;
    /**
     * @var array|null
     */
    public array|null $benefitTypePrivateMedicalInsurance;

    /**
     * @var array|null
     */
    private array|null $_benefitTypeDental;
    /**
     * @var array|null
     */
    private array|null $_benefitTypeGroupCriticalIllnessCover;
    /**
     * @var array|null
     */
    private array|null $_benefitTypeGroupDeathInService;
    /**
     * @var array|null
     */
    private array|null $_benefitTypeGroupIncomeProtection;
    /**
     * @var array|null
     */
    private array|null $_benefitTypeGroupLifeAssurance;
    /**
     * @var array|null
     */
    private array|null $_benefitTypeHealthCashPlan;
    /**
     * @var array|null
     */
    private array|null $_benefitTypePrivateMedicalInsurance;

    // Static Methods
    // =========================================================================

    /**
     * Returns the display name of this class.
     *
     * @return string The display name of this class.
     */
    public static function displayName(): string
    {
        return Craft::t('staff-management', 'Benefit Type');
    }

    /**
     * @inheritdoc
     */
    public static function lowerDisplayName(): string
    {
        return Craft::t('staff-management', 'benefit type');
    }

    /**
     * @inheritdoc
     */
    public static function pluralDisplayName(): string
    {
        return Craft::t('staff-management', 'Benefit Types');
    }

    /**
     * @inheritdoc
     */
    public static function pluralLowerDisplayName(): string
    {
        return Craft::t('staff-management', 'benefit type');
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
        return new BenefitProviderQuery(static::class);
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

//        $rules[] = [['name', 'content'], 'required'];

//        return $rules;
    }

    // Public Methods
    // =========================================================================

    // Indexes, etc.
    // -------------------------------------------------------------------------

    /**
     * @param mixed $context
     * @return string
     */
    public static function gqlTypeNameByContext($context): string
    {
        return 'BenefitType';
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
     * @param bool $isNew
     * @return bool
     */
    public function beforeSave(bool $isNew): bool
    {
        return parent::beforeSave($isNew); // TODO: Change the autogenerated stub
    }

    /**
     * Performs actions after an element is saved.
     *
     * @param bool $isNew Whether the element is brand new
     *
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
     * @param $isNew
     */
    private function _saveRecord($isNew): void
    {
        try{
            $fields = [];
            $fields['providerId'] = $this->providerId;
            $fields['status'] = $this->status;
            $fields['policyName'] = $this->policyName;
            $fields['policyNumber'] = $this->policyNumber;
            $fields['policyHolder'] = $this->policyHolder;
            $fields['content'] = $this->content;
            $fields['policyStartDate'] = $this->policyStartDate;
            $fields['policyRenewalDate'] = $this->policyRenewalDate;
            $fields['paymentFrequency'] = $this->paymentFrequency;
            $fields['commissionRate'] = $this->commissionRate;

            //save benefit type
            switch($this->benefitType) {
                case 'dental':
                    BenefitTypes::saveDental([$fields, ...$this->benefitTypeDental]);
            }

        } catch (\Exception $e) {
            $logger = new Logger();
            $logger->stdout(PHP_EOL, $logger::RESET);
            $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
            Craft::error($e->getMessage(), __METHOD__);
        }
    }
}
