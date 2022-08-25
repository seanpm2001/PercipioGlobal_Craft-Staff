<?php

namespace percipiolondon\staff\gql\types\generators;

use Craft;
use craft\gql\base\Generator;
use craft\gql\base\GeneratorInterface;
use craft\gql\base\ObjectType;
use craft\gql\base\SingleGeneratorInterface;
use craft\gql\GqlEntityRegistry;
use craft\gql\TypeManager;

use percipiolondon\staff\elements\Notification as NotificationElement;
use percipiolondon\staff\gql\arguments\elements\Notification as NotificationArguments;
use percipiolondon\staff\gql\interfaces\elements\Notification as NotificationInterface;
use percipiolondon\staff\gql\types\elements\Notification;


class NotificationGenerator extends Generator implements GeneratorInterface, SingleGeneratorInterface
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
        /** @var Notification $notification */

        $context = $context ?: Craft::$app->getFields()->getLayoutByType(NotificationElement::class);

        $typeName = NotificationElement::gqlTypeNameByContext(null);
        $contentFieldGqlTypes = self::getContentFields($context);

        $historyFields = TypeManager::prepareFieldDefinitions(array_merge(NotificationInterface::getFieldDefinitions(), $contentFieldGqlTypes), $typeName);
        $historyArgs = NotificationArguments::getArguments();

        return GqlEntityRegistry::getEntity($typeName) ?: GqlEntityRegistry::createEntity($typeName, new Notification([
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