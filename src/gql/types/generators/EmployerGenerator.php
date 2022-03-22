<?php
/**
 * @link https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license https://craftcms.github.io/license/
 */

namespace percipiolondon\staff\gql\types\generators;

use Craft;
use craft\gql\base\Generator;
use craft\gql\base\GeneratorInterface;
use craft\gql\base\ObjectType;
use craft\gql\base\SingleGeneratorInterface;
use craft\gql\GqlEntityRegistry;
use craft\gql\TypeManager;

use percipiolondon\staff\elements\Employer as EmployerElement;
use percipiolondon\staff\gql\arguments\elements\Employer as EmployerArguments;
use percipiolondon\staff\gql\interfaces\elements\Employer as EmployerInterface;
use percipiolondon\staff\gql\types\elements\Employer;

/**
 * Class EmployerGenerator
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class EmployerGenerator extends Generator implements GeneratorInterface, SingleGeneratorInterface
{
    /**
     * @inheritdoc
     */
    public static function generateTypes($context = null): array
    {
        // Employers have no context
        $type = static::generateType($context);
        return [$type->name => $type];
    }

    /**
     * @inheritdoc
     */
    public static function generateType($context): ObjectType
    {
        /** @var Employer $employer */

        $context = $context ?: Craft::$app->getFields()->getLayoutByType(EmployerElement::class);

        $typeName = EmployerElement::gqlTypeNameByContext(null);
        $contentFieldGqlTypes = self::getContentFields($context);

        $employerFields = TypeManager::prepareFieldDefinitions(array_merge(EmployerInterface::getFieldDefinitions(), $contentFieldGqlTypes), $typeName);
        $employerArgs = EmployerArguments::getArguments();

        return GqlEntityRegistry::getEntity($typeName) ?: GqlEntityRegistry::createEntity($typeName, new Employer([
            'name' => $typeName,
            'args' => function () use ($employerArgs) {
                return $employerArgs;
            },
            'fields' => function() use ($employerFields) {
                return $employerFields;
            },
        ]));
    }
}