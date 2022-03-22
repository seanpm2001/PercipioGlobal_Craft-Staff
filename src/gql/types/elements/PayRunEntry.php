<?php

namespace percipiolondon\staff\gql\types\elements;

use craft\gql\types\elements\Element;
use craft\helpers\Json;

use GraphQL\Type\Definition\ResolveInfo;

use percipiolondon\staff\elements\PayRunEntry as PayRunEntryElement;
use percipiolondon\staff\gql\interfaces\elements\PayRunEntry as PayRunEntryInterface;

/**
 * Class PayRunEntry
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class PayRunEntry extends Element
{
    /**
     * @inheritdoc
     */
    public function __construct(array $config)
    {
        $config['interfaces'] = [
            PayRunEntryInterface::getType(),
        ];

        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    protected function resolve($source, $arguments, $context, ResolveInfo $resolveInfo)
    {
        /** @var PayRunEntryElement $source */

        $fieldName = $resolveInfo->fieldName;

        return match ($fieldName) {
            'nationalInsuranceCalculation' => Json::decodeIfJson($source->nationalInsuranceCalculation),
            'payOptions' => Json::decodeIfJson($source->payOptions),
            'pensionSummary' => Json::decodeIfJson($source->pensionSummary),
            'totals' => Json::decodeIfJson($source->totals),
            'periodOverrides' => Json::decodeIfJson($source->periodOverrides),
            'totalsYtd' => Json::decodeIfJson($source->totalsYtd),
            'totalsYtdOverrides' => Json::decodeIfJson($source->totalsYtdOverrides),
            'fps' => Json::decodeIfJson($source->fps),
            'umbrellaPayment' => Json::decodeIfJson($source->umbrellaPayment),
            'employee' => Json::decodeIfJson($source->employee),
            default => parent::resolve($source, $arguments, $context, $resolveInfo),
        };
    }
}
