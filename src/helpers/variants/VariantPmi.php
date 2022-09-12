<?php

namespace percipiolondon\staff\helpers\variants;

use craft\helpers\Db;
use craft\web\Request;
use percipiolondon\staff\records\BenefitVariantPmi;

class VariantPmi
{
    public static function fill(?int $variantId, Request $request): BenefitVariantPmi
    {
        $benefit = BenefitVariantPmi::findOne($variantId);

        if (is_null($benefit)) {
            $benefit = new BenefitVariantPmi();
            $benefit->id = $variantId;
        }

        $benefit->underwritingBasis = $request->getBodyParam('underwritingBasis');
        $benefit->hospitalList = $request->getBodyParam('hospitalList');

        return $benefit;
    }

    public static function get(?int $variantId, Request $request): array
    {
        $benefit = [];
        $benefit['id'] = $variantId;
        $benefit['name'] = $request->getBodyParam('name');
        $benefit['underwritingBasis'] = $request->getBodyParam('underwritingBasis');
        $benefit['hospitalList'] = $request->getBodyParam('hospitalList');

        return $benefit;
    }
}