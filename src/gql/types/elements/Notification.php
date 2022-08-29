<?php

namespace percipiolondon\staff\gql\types\elements;

use craft\gql\types\elements\Element;

use GraphQL\Type\Definition\ResolveInfo;

use percipiolondon\staff\elements\Notification as NotificationElement;
use percipiolondon\staff\gql\interfaces\elements\Notification as NotificationInterface;

/**
 * Class Request
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class Notification extends Element
{
    /**
     * @inheritdoc
     */
    public function __construct(array $config)
    {
        $config['interfaces'] = [
            NotificationInterface::getType(),
        ];

        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    protected function resolve($source, $arguments, $context, ResolveInfo $resolveInfo)
    {
        /** @var NotificationElement $source */
        $fieldName = $resolveInfo->fieldName;

        return match ($fieldName) {
            default => parent::resolve($source, $arguments, $context, $resolveInfo),
        };
    }
}
