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
use percipiolondon\craftstaff\gql\interfaces\elements\Employee as EmployeeInterface;
use craft\gql\TypeManager;

use percipiolondon\craftstaff\elements\Employee as EmployeeElement;
use percipiolondon\craftstaff\gql\types\elements\Employee;

/**
 * Class AssetType
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 3.3.0
 */
class EmployeeType extends Generator implements GeneratorInterface, SingleGeneratorInterface
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
        /** @var Employee $employee */

        $context = $context ?: Craft::$app->getFields()->getLayoutByType(EmployeeElement::class);

        $typeName = EmployeeElement::gqlTypeNameByContext(null);
        $contentFieldGqlTypes = self::getContentFields($context);

        $employeeFields = TypeManager::prepareFieldDefinitions(array_merge(EmployeeInterface::getFieldDefinitions(), $contentFieldGqlTypes), $typeName);

        return GqlEntityRegistry::getEntity($typeName) ?: GqlEntityRegistry::createEntity($typeName, new Employee([
            'name' => $typeName,
            'fields' => function() use ($employeeFields) {
                return $employeeFields;
            },
        ]));
    }
}