<?php
/**
 * @link https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license https://craftcms.github.io/license/
 */

namespace percipiolondon\craftstaff\gql\types\generators;

use Craft;
use craft\gql\base\Generator;
use craft\gql\base\GeneratorInterface;
use craft\gql\base\ObjectType;
use craft\gql\base\SingleGeneratorInterface;
use craft\gql\GqlEntityRegistry;
use craft\gql\TypeManager;

use percipiolondon\craftstaff\elements\Employer as EmployerElement;
use percipiolondon\craftstaff\gql\interfaces\elements\Employer as EmployerInterface;
use percipiolondon\craftstaff\gql\types\elements\Employer;

/**
 * Class EmployerType
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class EmployerType extends Generator implements GeneratorInterface, SingleGeneratorInterface
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

        return GqlEntityRegistry::getEntity($typeName) ?: GqlEntityRegistry::createEntity($typeName, new Employer([
            'name' => $typeName,
            'fields' => function() use ($employerFields) {
                return $employerFields;
            },
        ]));
    }
}