<?php

namespace percipiolondon\staff\elements;

use Craft;
use craft\elements\Asset;
use DateTime;
use craft\base\Element;
use craft\elements\db\ElementQueryInterface;
use craft\web\Request;
use percipiolondon\staff\db\Table;
use percipiolondon\staff\elements\db\BenefitVariantQuery;
use percipiolondon\staff\helpers\variants\VariantGcic;
use percipiolondon\staff\records\BenefitEmployeeVariant;
use percipiolondon\staff\records\BenefitPolicy;
use percipiolondon\staff\records\BenefitType;
use percipiolondon\staff\records\BenefitVariantGcic;
use percipiolondon\staff\records\TotalRewardsStatement;
use percipiolondon\staff\records\BenefitVariant as BenefitVariantRecord;
use yii\base\BaseObject;
use yii\web\NotFoundHttpException;

/**
 *
 * @property-read string $gqlTypeName
 * @property-read null|array $provider
 * @property-read null|array $employees
 * @property-read null|\percipiolondon\staff\records\BenefitPolicy $policy
 * @property-read \percipiolondon\staff\records\TotalRewardsStatement|null $totalRewardsStatement
 */
class BenefitVariant extends Element
{
    public ?TotalRewardsStatement $trs = null;
    public ?int $policyId = null;
    public ?Request $request = null;
    public ?string $name = null;

    private ?TotalRewardsStatement $_totalRewardsStatement = null;
    private ?BenefitPolicy $_policy = null;
    private ?array $_provider = null;
    private ?array $_employees = null;
    private ?string $_type = null;

    private ?BenefitVariantGcic $_gcic = null;

    /**
     * @return string
     */
    public static function displayName(): string
    {
        return Craft::t('staff-management', 'Benefit Variant');
    }

    /**
     * @return string
     */
    public static function lowerDisplayName(): string
    {
        return Craft::t('staff-management', 'benefit variant');
    }

    /**
     * @return string
     */
    public static function pluralDisplayName(): string
    {
        return Craft::t('staff-management', 'Benefit Variants');
    }

    /**
     * @return string
     */
    public static function pluralLowerDisplayName(): string
    {
        return Craft::t('staff-management', 'benefit variants');
    }

    /**
     * @return array
     */
    public function defineRules(): array
    {
        return parent::defineRules();
    }
    /**
     * @param mixed $context
     * @return string
     */
    public static function gqlTypeNameByContext($context): string
    {
        return 'BenefitVariant';
    }

    /**
     * @inheritdoc
     */
    public function getGqlTypeName(): string
    {
        return static::gqlTypeNameByContext($this);
    }

    /**
     * @return ElementQueryInterface
     */
    public static function find(): ElementQueryInterface
    {
        return new BenefitVariantQuery(static::class);
    }

    public function getTotalRewardsStatement(): ?TotalRewardsStatement
    {
        if ($this->_totalRewardsStatement === null) {
            if ($this->id === null) {
                return null;
            }

            $this->_totalRewardsStatement = TotalRewardsStatement::findOne(['variantId' => $this->id]);

            return $this->_totalRewardsStatement;
        }
    }

    public function getPolicy(): ?BenefitPolicy
    {
        if ($this->_policy === null) {
            if ($this->policyId === null) {
                return null;
            }

            $this->_policy = BenefitPolicy::findOne($this->policyId);
        }

        return $this->_policy;
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function getProvider(): ?array
    {
        if ($this->_provider === null) {
            if ($this->policyId === null) {
                return null;
            }

            $this->getPolicy();

            if ($this->_policy === null) {
                return null;
            }

            $this->_provider = BenefitProvider::findOne($this->_policy['providerId'])->toArray();

            if ($this->_provider) {
                $this->_provider['logo'] = Asset::findOne($this->_provider['logo'])?->getUrl();
            }
        }

        return $this->_provider;
    }

    public function getEmployees(): ?array
    {
        if ($this->_employees === null) {
            $variantEmployees = BenefitEmployeeVariant::findAll(['variantId' => $this->id]);

            $this->_employees = [];
            foreach($variantEmployees as $employee) {
                $this->_employees[] = Employee::findOne($employee);
            }

            return $this->_employees;
        }
    }

    public function getType(): ?string
    {
        $benefitType = BenefitType::findOne($this->getPolicy()->benefitTypeId ?? null);
        $this->_type = match ($benefitType->name ?? '') {
            'Dental' => 'dental',
            'Group Critical Illness Cover' => 'gcic',
            default => null
        };

        return $this->_type;
    }

    public function getGcic(): ?BenefitVariantGcic
    {
        if ($this->_gcic === null) {
            $this->_gcic = BenefitVariantGcic::findOne($this->id);
        }

        return $this->_gcic;
    }

    public function getFields(string $benefitTypeName): ?array
    {
        $variant = match ($benefitTypeName ?? '') {
            'Group Critical Illness Cover' => VariantGcic::getVariant($this->id, $this->request),
            default => null
        };

        if ($variant) {
            return $variant;
        }

        return null;
    }

    public function getValues(string $benefitTypeName): ?array
    {
        return match ($benefitTypeName ?? '') {
            'Group Critical Illness Cover' => $this->_gcic ? $this->_gcic->toArray() : $this->getGcic()->toArray(),
            default => []
        };
    }

    /**
     * @param bool $isNew
     */
    public function afterSave(bool $isNew): void
    {
        if (!$this->propagating) {
            $this->_saveRecord($isNew);
        }

        parent::afterSave($isNew); // TODO: Change the autogenerated stub
    }

    private function _saveRecord(bool $isNew): void
    {
        try {
            $policyId = $this->policyId;

            $policy = BenefitPolicy::findOne($policyId);

            if (is_null($policy)) {
                throw new NotFoundHttpException(Craft::t('staff-management', 'Policy does not exist'));
            }

            //save benefit variant generic
            $benefit = BenefitVariantRecord::findOne($this->id);

            if (is_null($benefit)) {
                $benefit = new BenefitVariantRecord();
                $benefit->id = $this->id;
            }

            $benefit->name = $this->name;
            $benefit->policyId = $this->policyId;
            $successBenefit = $benefit->save();

            if(!$successBenefit) {
                Craft::error(Craft::t('staff-management','The save of the Benefit Variant wasn\'t successfull'));
            }

            // save benefit type variants
            $benefitType = BenefitType::findOne($policy->benefitTypeId);
            $variant = match ($benefitType->name ?? '') {
                'Group Critical Illness Cover' => VariantGcic::saveVariant($this->id ?? null, $this->request),
                default => null
            };

            if ($variant) {
                $successVariant = $variant->save();

                if(!$successVariant) {
                    Craft::error(Craft::t('staff-management','The save of the Benefit Type specific Variant wasn\'t successfull'));
                }
            }

        } catch (\Exception $e) {
            Craft::error($e->getMessage(), __METHOD__);
        }
    }

}