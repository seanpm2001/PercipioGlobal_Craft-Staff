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

use DateTime;
use craft\db\ActiveRecord;
use percipiolondon\staff\db\Table;

/**
 * @property integer $id
 * @property integer $providerId
 * @property integer $employerId
 * @property integer $benefitTypeId
 * @property string $internalCode
 * @property string $status
 * @property string $policyName
 * @property string $policyNumber
 * @property string $policyHolder
 * @property string $description
 * @property DateTime $policyStartDate
 * @property DateTime $policyRenewalDate
 * @property string $paymentFrequency
 * @property float $commissionRate
 *
 */

class BenefitPolicy extends ActiveRecord
{

    // Public Static Methods
    // =========================================================================
    public function rules()
    {
        $rules = parent::rules();
        $rules[] = [[
            'internalCode',
            'providerId',
            'status',
            'policyName',
            'policyNumber',
            'policyHolder',
            'policyStartDate',
            'policyRenewalDate',
            'paymentFrequency',
            'commissionRate',
        ], 'required'];

        return $rules;
    }

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
        return Table::BENEFIT_POLICIES;
    }
}
