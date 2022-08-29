<?php

namespace percipiolondon\staff\gql\types\generators;

use Craft;

use craft\gql\base\Generator;
use craft\gql\base\GeneratorInterface;
use craft\gql\base\ObjectType;
use craft\gql\base\SingleGeneratorInterface;
use craft\gql\GqlEntityRegistry;
use craft\gql\TypeManager;

use percipiolondon\staff\records\Settings as SettingsElement;
use percipiolondon\staff\gql\interfaces\elements\Settings as SettingsInterface;
use percipiolondon\staff\gql\types\elements\Settings;

/**
 * Class RequestGenerator
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class SettingsGenerator extends Generator implements GeneratorInterface, SingleGeneratorInterface
{
    /**
     * @inheritdoc
     */
    public static function generateTypes($context = null): array
    {
        $type = static::generateType($context);
        return [$type->name => $type];
    }

    /**
     * @inheritdoc
     */
    public static function generateType($context): ObjectType
    {
        /** @var Settings $request */

        $context = $context ?: Craft::$app->getFields()->getLayoutByType(SettingsElement::class);

        $typeName = 'Settings';
        $contentFieldGqlTypes = self::getContentFields($context);

        $requestFields = TypeManager::prepareFieldDefinitions(array_merge(SettingsInterface::getFieldDefinitions(), $contentFieldGqlTypes), $typeName);

        return GqlEntityRegistry::getEntity($typeName) ?: GqlEntityRegistry::createEntity($typeName, new Settings([
            'name' => $typeName,
            'fields' => function() use ($requestFields) {
                return $requestFields;
            },
        ]));
    }
}
