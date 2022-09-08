<?php

namespace percipiolondon\staff\helpers\variants;

use craft\helpers\Db;
use craft\web\Request;
use percipiolondon\staff\records\BenefitVariantGla;

class VariantGla
{
    public static function fill(?int $variantId, Request $request): BenefitVariantGla
    {
        $benefit = BenefitVariantGla::findOne($variantId);

        if (is_null($benefit)) {
            $benefit = new BenefitVariantGla();
            $benefit->id = $variantId;
        }

        $benefit->rateReviewGuaranteeDate = Db::prepareDateForDb($request->getBodyParam('rateReviewGuaranteeDate'));
        $benefit->costingBasis = $request->getBodyParam('costingBasis');
        $benefit->unitRate = $request->getBodyParam('unitRate');
        $benefit->unitRateSuffix = $request->getBodyParam('unitRateSuffix');
        $benefit->freeCoverLevelAutomaticAcceptanceLimit = $request->getBodyParam('freeCoverLevelAutomaticAcceptanceLimit');
        $benefit->dateRefreshFrequency = $request->getBodyParam('dateRefreshFrequency');
        $benefit->pensionSchemeTaxReferenceNumber = $request->getBodyParam('pensionSchemeTaxReferenceNumber');
        $benefit->dateOfTrustDeed = Db::prepareDateForDb($request->getBodyParam('dateOfTrustDeed'));
        $benefit->eventLimit = $request->getBodyParam('eventLimit');

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
        $benefit['pensionSchemeTaxReferenceNumber'] = $request->getBodyParam('pensionSchemeTaxReferenceNumber');
        $benefit['dateOfTrustDeed'] = Db::prepareDateForDb($request->getBodyParam('dateOfTrustDeed'));
        $benefit['eventLimit'] = $request->getBodyParam('eventLimit');

        return $benefit;
    }
}