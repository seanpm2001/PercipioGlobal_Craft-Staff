<?php
/**
 * staff-management plugin for Craft CMS 3.x
 *
 * Craft Staff Management provides an HR solution for payroll and benefits
 *
 * @link      http://percipio.london
 * @copyright Copyright (c) 2021 Percipio
 */

namespace percipiolondon\staff\records;

use craft\db\ActiveRecord;
use percipiolondon\staff\db\Table;

/**
 * @property \DateTime $rateReviewGuaranteeDate
 * @property string $costingBasis
 * @property float $unitRate
 * @property string $unitRateSuffix
 * @property float $freeCoverLevelAutomaticAcceptanceLimit
 * @property string $dateRefreshFrequency
 * @property string $pensionSchemeTaxReferenceNumber
 * @property \DateTime $dateOfTrustDeed
 * @property float $eventLimit
 *
 */

class BenefitVariantGdis extends ActiveRecord
{
//    public function rules(): array
//    {
//        $rules = parent::rules();
//        $rules[] = [['dateRefreshFrequency'], 'required'];
//
//        return $rules;
//    }

    /**
     * Declares the name of the database table associated with this AR class.
     * By default this method returns the class name as the table name by calling [[Inflector::camel2id()]]
     * with prefix [[Connection::tablePrefix]]. For example if [[Connection::tablePrefix]] is `tbl_`,
     * `Customer` becomes `tbl_customer`, and `OrderItem` becomes `tbl_order_item`. You may override this method
     * if the table is not named after this convention.
     *
     * By convention, tables created by plugins should be prefixed with the plugin
     * name and an underscore.
     *
     * @return string the table name
     */
    public static function tableName(): string
    {
        return Table::BENEFIT_VARIANT_GDIS;
    }
}
