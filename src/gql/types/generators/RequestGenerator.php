<?php

namespace percipiolondon\staff\gql\types\generators;

use Craft;

use craft\gql\base\Generator;
use craft\gql\base\GeneratorInterface;
use craft\gql\base\ObjectType;
use craft\gql\base\SingleGeneratorInterface;
use craft\gql\GqlEntityRegistry;
use craft\gql\TypeManager;

use percipiolondon\staff\elements\Request as RequestElement;
use percipiolondon\staff\gql\arguments\elements\Request as RequestArguments;
use percipiolondon\staff\gql\interfaces\elements\Request as RequestInterface;
use percipiolondon\staff\gql\types\elements\Request;

/**
 * Class RequestGenerator
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class RequestGenerator extends Generator implements GeneratorInterface, SingleGeneratorInterface
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
        /** @var Request $request */

        $context = $context ?: Craft::$app->getFields()->getLayoutByType(RequestElement::class);

        $typeName = RequestElement::gqlTypeNameByContext(null);
        $contentFieldGqlTypes = self::getContentFields($context);

        $requestFields = TypeManager::prepareFieldDefinitions(array_merge(RequestInterface::getFieldDefinitions(), $contentFieldGqlTypes), $typeName);
        $requestArgs = RequestArguments::getArguments();

        return GqlEntityRegistry::getEntity($typeName) ?: GqlEntityRegistry::createEntity($typeName, new Request([
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
