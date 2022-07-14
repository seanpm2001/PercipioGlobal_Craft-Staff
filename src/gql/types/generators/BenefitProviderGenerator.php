<?php

namespace percipiolondon\staff\gql\types\generators;

use Craft;

use craft\gql\base\Generator;
use craft\gql\base\GeneratorInterface;
use craft\gql\base\ObjectType;
use craft\gql\base\SingleGeneratorInterface;
use craft\gql\GqlEntityRegistry;
use craft\gql\TypeManager;

use percipiolondon\staff\elements\BenefitProvider as BenefitProviderElement;
use percipiolondon\staff\gql\arguments\elements\BenefitProvider as BenefitProviderArguments;
use percipiolondon\staff\gql\interfaces\elements\BenefitProvider as BenefitProviderInterface;
use percipiolondon\staff\gql\types\elements\BenefitProvider;

/**
 * Class BenefitProviderGenerator
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class BenefitProviderGenerator extends Generator implements GeneratorInterface, SingleGeneratorInterface
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
        /** @var BenefitProvider $benefitProvider */

        $context = $context ?: Craft::$app->getFields()->getLayoutByType(BenefitProviderElement::class);

        $typeName = BenefitProviderElement::gqlTypeNameByContext(null);
        $contentFieldGqlTypes = self::getContentFields($context);

        $benefitProviderFields = TypeManager::prepareFieldDefinitions(array_merge(BenefitProviderInterface::getFieldDefinitions(), $contentFieldGqlTypes), $typeName);
        $benefitProviderArgs = BenefitProviderArguments::getArguments();

        return GqlEntityRegistry::getEntity($typeName) ?: GqlEntityRegistry::createEntity($typeName, new BenefitProvider([
            'name' => $typeName,
            'args' => function() use ($benefitProviderArgs) {
                return $benefitProviderArgs;
            },
            'fields' => function() use ($benefitProviderFields) {
                return $benefitProviderFields;
            },
        ]));
    }
}
