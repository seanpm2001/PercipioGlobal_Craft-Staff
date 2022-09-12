<?php

namespace percipiolondon\staff\gql\types\generators;

use Craft;

use craft\gql\base\Generator;
use craft\gql\base\GeneratorInterface;
use craft\gql\base\ObjectType;
use craft\gql\base\SingleGeneratorInterface;
use craft\gql\GqlEntityRegistry;
use craft\gql\TypeManager;

use percipiolondon\staff\elements\BenefitVariant as Element;
use percipiolondon\staff\gql\arguments\elements\BenefitVariant as Arguments;
use percipiolondon\staff\gql\interfaces\elements\BenefitVariant as ElementInterface;
use percipiolondon\staff\gql\types\elements\BenefitVariant as ElementType;

/**
 * Class BenefitProviderGenerator
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class BenefitVariantGenerator extends Generator implements GeneratorInterface, SingleGeneratorInterface
{
    /**
     * @inheritdoc
     */
    public static function generateTypes($context = null): array
    {
        // Employees have no context
        $type = static::generateType($context);
        return [$type->name => $type];
    }

    /**
     * @inheritdoc
     */
    public static function generateType($context): ObjectType
    {
        /** @var ElementType $elementType */

        $context = $context ?: Craft::$app->getFields()->getLayoutByType(Element::class);

        $typeName = Element::gqlTypeNameByContext(null);
        $contentFieldGqlTypes = self::getContentFields($context);

        $fieldsGenerator = TypeManager::prepareFieldDefinitions(array_merge(ElementInterface::getFieldDefinitions(), $contentFieldGqlTypes), $typeName);
        $argumentsGenerator = Arguments::getArguments();

        return GqlEntityRegistry::getEntity($typeName) ?: GqlEntityRegistry::createEntity($typeName, new ElementType([
            'name' => $typeName,
            'args' => function() use ($argumentsGenerator) {
                return $argumentsGenerator;
            },
            'fields' => function() use ($fieldsGenerator) {
                return $fieldsGenerator;
            },
        ]));
    }
}
