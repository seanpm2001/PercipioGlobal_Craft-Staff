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

use craft\helpers\DateTimeHelper;
use DateTime;
use Craft;
use craft\base\Element;
use craft\elements\db\ElementQueryInterface;

use percipiolondon\staff\elements\db\BenefitTypeQuery;
use percipiolondon\staff\helpers\BenefitTypes;
use percipiolondon\staff\helpers\Logger;
use percipiolondon\staff\Staff;

/**
 * Benefit type Element
 *
 * @property-read string $gqlTypeName
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
    public string|null $internalCode = null;
    /**
     * @var string|null
     */
    public string|null $status = null;
    /**
     * @var string|null
     */
    public string|null $policyName = null;
    /**
     * @var string|null
     */
    public string|null $policyNumber = null;
    /**
     * @var string|null
     */
    public string|null $policyHolder = null;
    /**
     * @var string|null
     */
    public string|null $content = null;
    /**
     * @var DateTime|bool|null
     */
    public DateTime|bool|null $policyStartDate = null;
    /**
     * @var DateTime|bool|null
     */
    public DateTime|bool|null $policyRenewalDate = null;
    /**
     * @var string|null
     */
    public string|null $paymentFrequency = null;
    /**
     * @var float|null
     */
    public float|null $commissionRate = null;

    /**
     * @var string|null
     */
    public string|null $benefitType = null;
    /**
     * @var array|null
     */
    public array|null $arrBenefitTypeDental = null;
    /**
     * @var array|null
     */
    public array|null $arrBenefitTypeGroupCriticalIllnessCover = null;
    /**
     * @var array|null
     */
    public array|null $arrBenefitTypeGroupDeathInService = null;
    /**
     * @var array|null
     */
    public array|null $arrBenefitTypeGroupIncomeProtection = null;
    /**
     * @var array|null
     */
    public array|null $arrBenefitTypeGroupLifeAssurance = null;
    /**
     * @var array|null
     */
    public array|null $arrBenefitTypeHealthCashPlan = null;
    /**
     * @var array|null
     */
    public array|null $arrBenefitTypePrivateMedicalInsurance = null;

    /**
     * @var array|null
     */
    private array|null $_benefitTypeDental = null;
    /**
     * @var array|null
     */
    private array|null $_benefitTypeGroupCriticalIllnessCover = null;
    /**
     * @var array|null
     */
    private array|null $_benefitTypeGroupDeathInService = null;
    /**
     * @var array|null
     */
    private array|null $_benefitTypeGroupIncomeProtection = null;
    /**
     * @var array|null
     */
    private array|null $_benefitTypeGroupLifeAssurance = null;
    /**
     * @var array|null
     */
    private array|null $_benefitTypeHealthCashPlan = null;
    /**
     * @var array|null
     */
    private array|null $_benefitTypePrivateMedicalInsurance = null;


    // Public Methods
    // =========================================================================
    public function getBenefitTypeDental(): bool|array|null
    {
        if(is_null($this->_benefitTypeDental)) {
            if(is_null($this->id)) {
                return null;
            }

            if( ($this->_benefitTypeDental = Staff::$plugin->groupBenefits->getBenefitTypeData($this->id, $this->benefitType)) === null) {
                $this->_benefitTypeDental = null;
            }
        }

        return $this->_benefitTypeDental ?: null;
    }

    public function getBenefitTypeGroupCriticalIllnessCover(): bool|array|null
    {
        if(is_null($this->_benefitTypeGroupCriticalIllnessCover)) {
            if(is_null($this->id)) {
                return null;
            }

            if( ($this->_benefitTypeGroupCriticalIllnessCover = Staff::$plugin->groupBenefits->getBenefitTypeData($this->id, $this->benefitType)) === null) {
                $this->_benefitTypeGroupCriticalIllnessCover = null;
            }
        }

        return $this->_benefitTypeGroupCriticalIllnessCover ?: null;
    }

    public function getBenefitTypeGroupDeathInService(): bool|array|null
    {
        if(is_null($this->_benefitTypeGroupDeathInService)) {
            if(is_null($this->id)) {
                return null;
            }

            if( ($this->_benefitTypeGroupDeathInService = Staff::$plugin->groupBenefits->getBenefitTypeData($this->id, $this->benefitType)) === null) {
                $this->_benefitTypeGroupDeathInService = null;
            }
        }

        return $this->_benefitTypeGroupDeathInService ?: null;
    }


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
        return new BenefitTypeQuery(static::class);
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
        $rules = parent::rules();

        $rules[] = [[
            'providerId',
            'internalCode',
            'status',
            'policyName',
            'policyNumber',
            'policyHolder',
            'policyStartDate',
            'policyRenewalDate',
            'paymentFrequency',
            'commissionRate',
            'benefitType'
        ], 'required'];

        $rules[] = ['arrBenefitTypeGroupDeathInService', 'validateBenefitType'];

        return $rules;
    }

    // Public Methods
    // =========================================================================
    public function validateBenefitType() {

        switch($this->benefitType) {
            case 'group-death-in-service':
                if(!DateTimeHelper::toDateTime($this->arrBenefitTypeGroupDeathInService['rateReviewGuaranteeDate'] ?? null)) {
                    $this->addError('rateReviewGuaranteeDate', "There's no rate review guarantee date selected");
                }
        }

//        if(!DateTimeHelper::toDateTime($this->arrBenefitTypeGroupDeathInService['rateReviewGuaranteeDate'] ?? null)) {
//            $this->addError('rateReviewGuaranteeDate', "There's no rate review guarantee date selected");
//        }
    }


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
            $fields['id'] = $this->id;
            $fields['providerId'] = $this->providerId;
            $fields['internalCode'] = $this->internalCode;
            $fields['status'] = $this->status;
            $fields['policyName'] = $this->policyName;
            $fields['policyNumber'] = $this->policyNumber;
            $fields['policyHolder'] = $this->policyHolder;
            $fields['content'] = $this->content;
            $fields['policyStartDate'] = $this->policyStartDate;
            $fields['policyRenewalDate'] = $this->policyRenewalDate;
            $fields['paymentFrequency'] = $this->paymentFrequency;
            $fields['commissionRate'] = $this->commissionRate;

            $benefitTypes = new BenefitTypes();

            //save benefit type
            switch($this->benefitType) {
                case 'group-critical-illness-cover':
                    $fields = array_merge($fields, $this->arrBenefitTypeGroupCriticalIllnessCover);
                    $benefitTypes->setGroupCriticalIllnessCover($fields);
                    break;
                case 'group-death-in-service':
                    $fields = array_merge($fields, $this->arrBenefitTypeGroupDeathInService);
                    $benefitTypes->setGroupDeathInService($fields);
                    break;
            }

        } catch (\Exception $e) {
            $logger = new Logger();
            $logger->stdout(PHP_EOL, $logger::RESET);
            $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
            Craft::error($e->getMessage(), __METHOD__);
        }
    }
}
