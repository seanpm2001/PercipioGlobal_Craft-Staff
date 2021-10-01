<?php

namespace percipiolondon\craftstaff\gql\types\elements;

use craft\gql\types\elements\Element;
use craft\helpers\Json;

use GraphQL\Type\Definition\ResolveInfo;

use percipiolondon\craftstaff\elements\PayRunEntry as PayRunEntryElement;
use percipiolondon\craftstaff\gql\interfaces\elements\PayRunEntry as PayRunEntryInterface;

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

        switch($fieldName) {
            case 'nationalInsuranceCalculation':
                return Json::decodeIfJson($source->nationalInsuranceCalculation);
            case 'payOptions':
                return Json::decodeIfJson($source->payOptions);
            case 'pensionSummary':
                return Json::decodeIfJson($source->pensionSummary);
            case 'totals':
                return Json::decodeIfJson($source->totals);
            case 'periodOverrides':
                return Json::decodeIfJson($source->periodOverrides);
            case 'totalsYtd':
                return Json::decodeIfJson($source->totalsYtd);
            case 'totalsYtdOverrides':
                return Json::decodeIfJson($source->totalsYtdOverrides);
            case 'fps':
                return Json::decodeIfJson($source->fps);
            case 'umbrellaPayment':
                return Json::decodeIfJson($source->umbrellaPayment);
            case 'employee':
                return Json::decodeIfJson($source->employee);
        }

        return parent::resolve($source, $arguments, $context, $resolveInfo);
    }
}
