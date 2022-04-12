<?php

namespace percipiolondon\staff\gql\types\generators;

use Craft;

use craft\gql\base\Generator;
use craft\gql\base\GeneratorInterface;
use craft\gql\base\ObjectType;
use craft\gql\base\SingleGeneratorInterface;
use craft\gql\GqlEntityRegistry;
use craft\gql\TypeManager;

use percipiolondon\staff\elements\PayRun as PayRunElement;
use percipiolondon\staff\gql\arguments\elements\PayRun as PayRunArguments;
use percipiolondon\staff\gql\interfaces\elements\PayRun as PayRunInterface;
use percipiolondon\staff\gql\types\elements\PayRun;

/**
 * Class PayRunGenerator
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class PayRunGenerator extends Generator implements GeneratorInterface, SingleGeneratorInterface
{
    /**
     * @inheritdoc
     */
    public static function generateTypes($context = null): array
    {
        // Payruns have no context
        $type = static::generateType($context);
        return [$type->name => $type];
    }

    /**
     * @inheritdoc
     */
    public static function generateType($context): ObjectType
    {
        /** @var Payrun $payrun */

        $context = $context ?: Craft::$app->getFields()->getLayoutByType(PayRunElement::class);

        $typeName = PayRunElement::gqlTypeNameByContext(null);
        $contentFieldGqlTypes = self::getContentFields($context);

        $payRunFields = TypeManager::prepareFieldDefinitions(array_merge(PayRunInterface::getFieldDefinitions(), $contentFieldGqlTypes), $typeName);
        $payRunArgs = PayRunArguments::getArguments();

        return GqlEntityRegistry::getEntity($typeName) ?: GqlEntityRegistry::createEntity($typeName, new PayRun([
            'name' => $typeName,
            'args' => function() use ($payRunArgs) {
                return $payRunArgs;
            },
            'fields' => function() use ($payRunFields) {
                return $payRunFields;
            },
        ]));
    }
}
