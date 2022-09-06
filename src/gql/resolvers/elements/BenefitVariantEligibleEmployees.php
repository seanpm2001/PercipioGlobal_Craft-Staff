<?php

namespace percipiolondon\staff\gql\resolvers\elements;

use Craft;
use craft\gql\base\ElementResolver;

use percipiolondon\staff\elements\Employee;
use percipiolondon\staff\helpers\Gql as GqlHelper;
use percipiolondon\staff\records\BenefitEmployeeVariant;
use percipiolondon\staff\records\BenefitPolicy;
use percipiolondon\staff\elements\BenefitVariant;

class BenefitVariantEligibleEmployees extends ElementResolver
{
    public static function prepareQuery($source, array $arguments, $fieldName = null)
    {
        if ($source === null) {
            $employees = [];

            $policy = BenefitPolicy::findOne($arguments['policyId'] ?? null);

            if($policy) {
                $variantIds = [];
                $variantEmployeeIds = [];

                //get all variants from the policy and collect their ids
                $variants = BenefitVariant::findAll(['policyId' => $policy->id]);
                foreach ($variants as $variant) {
                    $variantIds[] = $variant['id'];
                }

                // employees assigned to variants within the policies
                $variantEmployees = BenefitEmployeeVariant::findAll(['variantId' => $variantIds]);
                foreach ($variantEmployees as $variantEmployee) {
                    $variantEmployeeIds[] = $variantEmployee['employeeId'];
                }
                $companyEmployees = Employee::findAll(['employerId' => $policy->employerId]);

                // collect eligible employees
                foreach ($companyEmployees as $employee) {
                    if(!in_array($employee->id, $variantEmployeeIds, true)) {
                        $employees[] = $employee;
                    }
                }
            }

            $query = $employees;
        } else {
            $query = $source->$fieldName;
        }

        if (is_array($query)) {
            return $query;
        }

        foreach ($arguments as $key => $value) {
            $query->$key($value);
        }

        if (!GqlHelper::canQueryGroupBenefits()) {
            return [];
        }

        return $query->all();
    }
}
