<?php

namespace percipiolondon\staff\elements\db;

use craft\elements\db\ElementQuery;
use craft\helpers\Db;
use percipiolondon\staff\helpers\BenefitTypes;

/**
 * Class EmployeeQuery
 *
 * @package percipiolondon\staff\elements\db
 */
class BenefitTypeQuery extends ElementQuery
{
    /**
     * @var
     */
    public $benefitType;

    /**
     * @param $value
     * @return $this
     */
    public function benefitType($value)
    {
        $this->benefitType = $value;
        return $this;
    }

    /**
     * @return bool
     */
    protected function beforePrepare(): bool
    {
        $benefitTypesHelper = new BenefitTypes();

        if($this->benefitType) {

            //select the table name of the provided benefit types
            $table = $benefitTypesHelper->benefitTypesTables[$this->benefitType] ?? null;

            if($table) {
                $this->joinElementTable($table);

                $this->query->select([
                    $table.'.providerId',
                    $table.'.internalCode',
                    $table.'.status',
                    $table.'.policyName',
                    $table.'.policyNumber',
                    $table.'.policyHolder',
                    $table.'.content',
                    $table.'.policyStartDate',
                    $table.'.policyRenewalDate',
                    $table.'.paymentFrequency',
                    $table.'.commissionRate',
                    $table.'.benefitType',
                ]);
            }

        } else {

            $fields = [];

            foreach($benefitTypesHelper->benefitTypesTables as $type) {

                $this->leftJoin($type, '`'.$type.'`.`id` = `elements`.`id`');

                $fields[] = [
                    $type . '.providerId',
                    $type . '.internalCode',
                    $type . '.status',
                    $type . '.policyName',
                    $type . '.policyNumber',
                    $type . '.policyHolder',
                    $type . '.content',
                    $type . '.policyStartDate',
                    $type . '.policyRenewalDate',
                    $type . '.paymentFrequency',
                    $type . '.commissionRate',
                    $type . '.benefitType',
                ];
            }

            $this->query->select(array_merge(...$fields));
        }

        return parent::beforePrepare();
    }
}
