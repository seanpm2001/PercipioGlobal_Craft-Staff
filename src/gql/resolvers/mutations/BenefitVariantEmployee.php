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

        $policy = BenefitPolicy::findOne($arguments['policyId'] ?? null);

        if ($policy) {
            //get all variants from the policy and collect their ids
            $variants = BenefitVariant::findAll(['policyId' => $policy->id]);
            foreach ($variants as $variant) {
                $variantIds[] = $variant['id'];
            }

            $benefitVariantEmployee = BenefitEmployeeVariant::findOne(['variantId' => $variants, 'employeeId' => $arguments['employeeId']]);

            if (!$benefitVariantEmployee) {

            }
        }

        return $employee;
    }
}