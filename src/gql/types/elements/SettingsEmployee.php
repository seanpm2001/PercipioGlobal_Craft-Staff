<?php

namespace percipiolondon\staff\gql\types\elements;

use craft\gql\types\elements\Element;
use craft\helpers\Json;

use GraphQL\Type\Definition\ResolveInfo;

use percipiolondon\staff\elements\SettingsEmployee as SettingsEmployeeElement;
use percipiolondon\staff\gql\interfaces\elements\SettingsEmployee as SettingsEmployeeInterface;

/**
 * Class Request
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class SettingsEmployee extends Element
{
    /**
     * @inheritdoc
     */
    public function __construct(array $config)
    {
        $config['interfaces'] = [
            SettingsEmployeeInterface::getType(),
        ];

        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    protected function resolve($source, $arguments, $context, ResolveInfo $resolveInfo)
    {
        /** @var SettingsEmployeeElement $source */
        $fieldName = $resolveInfo->fieldName;

        return match ($fieldName) {
            default => parent::resolve($source, $arguments, $context, $resolveInfo),
        };
    }
}
