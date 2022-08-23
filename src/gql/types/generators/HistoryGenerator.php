<?php

namespace percipiolondon\staff\gql\types\generators;

use Craft;
use craft\gql\base\Generator;
use craft\gql\base\GeneratorInterface;
use craft\gql\base\ObjectType;
use craft\gql\base\SingleGeneratorInterface;
use craft\gql\GqlEntityRegistry;
use craft\gql\TypeManager;

use percipiolondon\staff\elements\History as HistoryElement;
use percipiolondon\staff\gql\arguments\elements\History as HistoryArguments;
use percipiolondon\staff\gql\interfaces\elements\History as HistoryInterface;
use percipiolondon\staff\gql\types\elements\History;


class HistoryGenerator extends Generator implements GeneratorInterface, SingleGeneratorInterface
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
        /** @var History $history */

        $context = $context ?: Craft::$app->getFields()->getLayoutByType(HistoryElement::class);

        $typeName = HistoryElement::gqlTypeNameByContext(null);
        $contentFieldGqlTypes = self::getContentFields($context);

        $historyFields = TypeManager::prepareFieldDefinitions(array_merge(HistoryInterface::getFieldDefinitions(), $contentFieldGqlTypes), $typeName);
        $historyArgs = HistoryArguments::getArguments();

        return GqlEntityRegistry::getEntity($typeName) ?: GqlEntityRegistry::createEntity($typeName, new History([
            'name' => $typeName,
            'args' => function() use ($historyArgs) {
                return $historyArgs;
            },
            'fields' => function() use ($historyFields) {
                return $historyFields;
            },
        ]));
    }
}