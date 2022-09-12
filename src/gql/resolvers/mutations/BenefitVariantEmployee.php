<?php

namespace percipiolondon\staff\gql\resolvers\mutations;

use Craft;
use craft\gql\base\ElementMutationResolver;
use GraphQL\Error\UserError;
use GraphQL\Type\Definition\ResolveInfo;

use percipiolondon\staff\elements\BenefitVariant;
use percipiolondon\staff\elements\Request as RequestElement;
use percipiolondon\staff\records\BenefitEmployeeVariant;
use percipiolondon\staff\records\BenefitPolicy;

class BenefitVariantEmployee extends ElementMutationResolver
{
    protected $immutableAttributes = ['id', 'uid'];

    public function addEmployee($source, array $arguments, $context, ResolveInfo $resolveInfo)
    {
        $employee = null;
        $variantIds = [];

        $variant = BenefitVariant::findOne($arguments['variantId']);
        $policy = BenefitPolicy::findOne($variant['policyId'] ?? null);

        if ($policy) {
            //get all variants from the policy and collect their ids
            $variants = BenefitVariant::findAll(['policyId' => $policy->id]);
            foreach ($variants as $variant) {
                $variantIds[] = $variant['id'];
            }

            $benefitVariantEmployee = BenefitEmployeeVariant::findOne(['variantId' => $variantIds, 'employeeId' => $arguments['employeeId']]);

            if (is_null($benefitVariantEmployee)) {
                $employee = new BenefitEmployeeVariant();
                $employee->employeeId = $arguments['employeeId'];
                $employee->variantId = $arguments['variantId'];
                $employee->save();
            }
        }

        return $employee;
    }

    public function removeEmployee($source, array $arguments, $context, ResolveInfo $resolveInfo)
    {
        $success = false;
        $variantEmployee = BenefitEmployeeVariant::findOne(['variantId' => $arguments['variantId'], 'employeeId' => $arguments['employeeId']]);

        if ($variantEmployee) {
            $success = $variantEmployee->delete();
        }

        return $success;
    }
}