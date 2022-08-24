<?php

namespace percipiolondon\staff\gql\types\generators;

use Craft;

use craft\gql\base\Generator;
use craft\gql\base\GeneratorInterface;
use craft\gql\base\ObjectType;
use craft\gql\base\SingleGeneratorInterface;
use craft\gql\GqlEntityRegistry;
use craft\gql\TypeManager;

use percipiolondon\staff\elements\SettingsEmployee as SettingsEmployeeElement;
use percipiolondon\staff\gql\arguments\elements\SettingsEmployee as SettingsEmployeeArguments;
use percipiolondon\staff\gql\interfaces\elements\SettingsEmployee as SettingsEmployeeInterface;
use percipiolondon\staff\gql\types\elements\SettingsEmployee;

/**
 * Class RequestGenerator
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class SettingsEmployeeGenerator extends Generator implements GeneratorInterface, SingleGeneratorInterface
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
        /** @var SettingsEmployee $request */

        $context = $context ?: Craft::$app->getFields()->getLayoutByType(SettingsEmployeeElement::class);

        $typeName = SettingsEmployeeElement::gqlTypeNameByContext(null);
        $contentFieldGqlTypes = self::getContentFields($context);

        $requestFields = TypeManager::prepareFieldDefinitions(array_merge(SettingsEmployeeInterface::getFieldDefinitions(), $contentFieldGqlTypes), $typeName);
        $requestArgs = SettingsEmployeeArguments::getArguments();

        return GqlEntityRegistry::getEntity($typeName) ?: GqlEntityRegistry::createEntity($typeName, new SettingsEmployee([
            'name' => $typeName,
            'args' => function() use ($requestArgs) {
                return $requestArgs;
            },
            'fields' => function() use ($requestFields) {
                return $requestFields;
            },
        ]));
    }
}
