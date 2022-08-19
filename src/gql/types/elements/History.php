<?php

namespace percipiolondon\staff\gql\types\elements;

use craft\gql\types\elements\Element;
use craft\helpers\Json;

use GraphQL\Type\Definition\ResolveInfo;

use percipiolondon\staff\elements\History as HistoryElement;
use percipiolondon\staff\gql\interfaces\elements\History as HistoryInterface;

/**
 * Class History
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class History extends Element
{
    /**
     * @inheritdoc
     */
    public function __construct(array $config)
    {
        $config['interfaces'] = [
            HistoryInterface::getType(),
        ];

        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    protected function resolve($source, $arguments, $context, ResolveInfo $resolveInfo)
    {
        /** @var HistoryElement $source */
        $fieldName = $resolveInfo->fieldName;

        return match ($fieldName) {
            default => parent::resolve($source, $arguments, $context, $resolveInfo),
        };
    }
}
