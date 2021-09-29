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
            case 'totals':
                return Json::decodeIfJson($source->totals);
        }

        return parent::resolve($source, $arguments, $context, $resolveInfo);
    }
}