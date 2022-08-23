<?php

namespace percipiolondon\staff\gql\types\elements;

use craft\gql\types\elements\Element;
use craft\helpers\Json;

use GraphQL\Type\Definition\ResolveInfo;

use percipiolondon\staff\elements\BenefitProvider as BenefitProviderElement;
use percipiolondon\staff\gql\interfaces\elements\BenefitProvider as BenefitProviderInterface;

/**
 * Class BenefitProvider
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class BenefitProvider extends Element
{
    /**
     * @inheritdoc
     */
    public function __construct(array $config)
    {
        $config['interfaces'] = [
            BenefitProviderInterface::getType(),
        ];

        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    protected function resolve($source, $arguments, $context, ResolveInfo $resolveInfo)
    {
        /** @var BenefitProviderElement $source */

        return parent::resolve($source, $arguments, $context, $resolveInfo);
    }
}
