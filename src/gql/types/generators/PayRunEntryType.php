<?php

namespace percipiolondon\craftstaff\gql\types\generators;

use Craft;

use craft\gql\base\Generator;
use craft\gql\base\GeneratorInterface;
use craft\gql\base\ObjectType;
use craft\gql\base\SingleGeneratorInterface;
use craft\gql\GqlEntityRegistry;
use craft\gql\TypeManager;

use percipiolondon\craftstaff\gql\arguments\elements\PayRunEntry as PayRunEntryArguments;
use percipiolondon\craftstaff\elements\PayRunEntry as PayRunEntryElement;
use percipiolondon\craftstaff\gql\interfaces\elements\PayRunEntry as PayRunEntryInterface;
use percipiolondon\craftstaff\gql\types\elements\PayRunEntry;

/**
 * Class PayRunEntryType
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class PayRunEntryType extends Generator implements GeneratorInterface, SingleGeneratorInterface
{
    /**
     * @inheritdoc
     */
    public static function generateTypes($context = null): array
    {
        // Payrun Entries have no context
        $type = static::generateType($context);
        return [$type->name => $type];
    }

    /**
     * @inheritdoc
     */
    public static function generateType($context): ObjectType
    {
        /** @var PayrunEntry $payrunEntry */

        $context = $context ?: Craft::$app->getFields()->getLayoutByType(PayRunEntryElement::class);

        $typeName = PayRunEntryElement::gqlTypeNameByContext(null);
        $contentFieldGqlTypes = self::getContentFields($context);

        $payRunEntryFields = TypeManager::prepareFieldDefinitions(array_merge(PayRunEntryInterface::getFieldDefinitions(), $contentFieldGqlTypes), $typeName);
        $payRunEntryArgs = PayRunEntryArguments::getArguments();

        return GqlEntityRegistry::getEntity($typeName) ?: GqlEntityRegistry::createEntity($typeName, new PayRunEntry([
            'name' => $typeName,
            'args' => function () use ($payRunEntryArgs) {
                return $payRunEntryArgs;
            },
            'fields' => function() use ($payRunEntryFields) {
                return $payRunEntryFields;
            },
        ]));
    }
}