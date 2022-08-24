<?php

namespace percipiolondon\staff\gql\types\elements;

use craft\gql\types\elements\Element;
use craft\helpers\Json;

use GraphQL\Type\Definition\ResolveInfo;

use percipiolondon\staff\records\Settings as SettingsElement;
use percipiolondon\staff\gql\interfaces\elements\Settings as SettingsInterface;

/**
 * Class Request
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class Settings extends Element
{
    /**
     * @inheritdoc
     */
    public function __construct(array $config)
    {
        $config['interfaces'] = [
            SettingsInterface::getType(),
        ];

        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    protected function resolve($source, $arguments, $context, ResolveInfo $resolveInfo)
    {
        /** @var SettingsElement $source */
        $fieldName = $resolveInfo->fieldName;

        return match ($fieldName) {
            default => parent::resolve($source, $arguments, $context, $resolveInfo),
        };
    }
}
