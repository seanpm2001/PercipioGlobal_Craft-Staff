<?php

namespace percipiolondon\staff\gql\types\elements;

use craft\gql\types\elements\Element;
use craft\helpers\Json;

use GraphQL\Type\Definition\ResolveInfo;

use percipiolondon\staff\elements\BenefitVariant as CraftElement;
use percipiolondon\staff\gql\interfaces\elements\BenefitVariant as ElementInterface;

/**
 * Class BenefitProvider
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class BenefitVariant extends Element
{
    /**
     * @inheritdoc
     */
    public function __construct(array $config)
    {
        $config['interfaces'] = [
            ElementInterface::getType(),
        ];

        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    protected function resolve($source, $arguments, $context, ResolveInfo $resolveInfo)
    {
        /** @var CraftElement $source */

        return parent::resolve($source, $arguments, $context, $resolveInfo);
    }
}
