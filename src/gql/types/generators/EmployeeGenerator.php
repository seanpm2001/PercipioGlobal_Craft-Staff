<?php

namespace percipiolondon\staff\gql\types\generators;

use Craft;

use craft\gql\base\Generator;
use craft\gql\base\GeneratorInterface;
use craft\gql\base\ObjectType;
use craft\gql\base\SingleGeneratorInterface;
use craft\gql\GqlEntityRegistry;
use craft\gql\TypeManager;

use percipiolondon\staff\elements\Employee as EmployeeElement;
use percipiolondon\staff\gql\arguments\elements\Employee as EmployeeArguments;
use percipiolondon\staff\gql\interfaces\elements\Employee as EmployeeInterface;
use percipiolondon\staff\gql\types\elements\Employee;

/**
 * Class EmployeeGenerator
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class EmployeeGenerator extends Generator implements GeneratorInterface, SingleGeneratorInterface
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
        $employeeArgs = EmployeeArguments::getArguments();

        return GqlEntityRegistry::getEntity($typeName) ?: GqlEntityRegistry::createEntity($typeName, new Employee([
            'name' => $typeName,
            'args' => function () use ($employeeArgs) {
                return $employeeArgs;
            },
            'fields' => function() use ($employeeFields) {
                return $employeeFields;
            },
        ]));
    }
}
