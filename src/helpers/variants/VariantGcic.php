<?php

namespace percipiolondon\staff\helpers\variants;

use Craft;
use craft\helpers\DateTimeHelper;
use craft\helpers\Db;
use craft\web\Request;
use percipiolondon\staff\records\BenefitVariantGcic;

class VariantGcic
{
    public static function fill(?int $variantId, Request $request): BenefitVariantGcic
    {
        $benefit = BenefitVariantGcic::findOne($variantId);

        if (is_null($benefit)) {
            $benefit = new BenefitVariantGcic();
            $benefit->id = $variantId;
        }

        $benefit->rateReviewGuaranteeDate = Db::prepareDateForDb($request->getBodyParam('rateReviewGuaranteeDate'));
        $benefit->costingBasis = $request->getBodyParam('costingBasis');
        $benefit->unitRate = $request->getBodyParam('unitRate');
        $benefit->unitRateSuffix = $request->getBodyParam('unitRateSuffix');
        $benefit->freeCoverLevelAutomaticAcceptanceLimit = $request->getBodyParam('freeCoverLevelAutomaticAcceptanceLimit');
        $benefit->dateRefreshFrequency = $request->getBodyParam('dateRefreshFrequency');

        return $benefit;
    }

    public static function get(?int $variantId, Request $request): array
    {
        $benefit = [];
        $benefit['id'] = $variantId;
        $benefit['name'] = $request->getBodyParam('name');
        $benefit['rateReviewGuaranteeDate'] = Db::prepareDateForDb($request->getBodyParam('rateReviewGuaranteeDate'));
        $benefit['costingBasis'] = $request->getBodyParam('costingBasis');
        $benefit['unitRate'] = $request->getBodyParam('unitRate');
        $benefit['unitRateSuffix'] = $request->getBodyParam('unitRateSuffix');
        $benefit['freeCoverLevelAutomaticAcceptanceLimit'] = $request->getBodyParam('freeCoverLevelAutomaticAcceptanceLimit');
        $benefit['dateRefreshFrequency'] = $request->getBodyParam('dateRefreshFrequency');

        return $benefit;
    }
}