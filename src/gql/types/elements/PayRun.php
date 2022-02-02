<?php

namespace percipiolondon\staff\gql\types\elements;

use craft\gql\types\elements\Element;
use craft\helpers\Json;

use GraphQL\Type\Definition\ResolveInfo;

use percipiolondon\staff\elements\PayRun as PayRunElement;
use percipiolondon\staff\gql\interfaces\elements\PayRun as PayRunInterface;

/**
 * Class PayRun
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class PayRun extends Element
{
    /**
     * @inheritdoc
     */
    public function __construct(array $config)
    {
        $config['interfaces'] = [
            PayRunInterface::getType(),
        ];

        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    protected function resolve($source, $arguments, $context, ResolveInfo $resolveInfo)
    {
        /** @var PayRunElement $source */

        $fieldName = $resolveInfo->fieldName;

        switch($fieldName) {
            case 'totals':
                return Json::decodeIfJson($source->totals);
        }

        return parent::resolve($source, $arguments, $context, $resolveInfo);
    }
}