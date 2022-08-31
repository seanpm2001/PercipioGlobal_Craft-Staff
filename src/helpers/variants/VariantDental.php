<?php

namespace percipiolondon\staff\helpers\variants;

use craft\web\Request;
use percipiolondon\staff\records\BenefitVariantDental;

class VariantDental
{
    public static function saveVariant(?int $variantId, ?int $trsId, int $policyId, Request $request): BenefitVariantDental
    {
        if ($variantId) {
            $benefit = BenefitVariantDental::findOne($variantId);
        } else {
            $benefit = new BenefitVariantDental();
        }

        $benefit->name = $request->getBodyParam('name');
        $benefit->trsId = $trsId;
        $benefit->policyId = $policyId;

        return $benefit;
    }
}