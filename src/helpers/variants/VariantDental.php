<?php

namespace percipiolondon\staff\helpers\variants;

use Craft;
use craft\web\Request;
use percipiolondon\staff\records\BenefitVariantDental;

class VariantDental
{
    public static function saveVariant(?int $variantId, ?int $trsId, int $policyId, Request $request): BenefitVariantDental
    {
        $benefit = BenefitVariantDental::findOne($variantId);

        if (is_null($benefit)) {
            $benefit = new BenefitVariantDental();
            $benefit->id = $variantId;
        }

        $benefit->name = $request->getBodyParam('name');
        $benefit->trsId = $trsId;
        $benefit->policyId = $policyId;

        return $benefit;
    }

    public static function getVariant(?int $variantId, Request $request): array
    {
        $benefit = [];
        $benefit['id'] = $variantId;
        $benefit['name'] = $request->getBodyParam('name');
        $benefit['trsId'] = $request->getBodyParam('trsId');
        $benefit['policyId'] = $request->getBodyParam('policyId');

        return $benefit;
    }
}