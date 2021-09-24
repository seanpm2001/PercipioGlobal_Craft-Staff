<?php

namespace percipiolondon\craftstaff\types\elements;

use craft\gql\base\Generator;
use craft\gql\base\GeneratorInterface;
use craft\gql\base\ObjectType;
use craft\gql\base\SingleGeneratorInterface;
use craft\gql\GqlEntityRegistry;
use craft\gql\TypeManager;
use percipiolondon\craftstaff\records\Employer;
use percipiolondon\craftstaff\elements\Employer as EmployerElement;
use percipiolondon\craftstaff\helpers\gql as GqlHelper;
use percipiolondon\craftstaff\gql\interfaces\elements\Employer as EmployerInterface;
use percipiolondon\craftstaff\gql\types\elements\Employer as EmployerElementType;

class EmployerType extends Generator implements GeneratorInterface, SingleGeneratorInterface
{
    public static function generateTypes($context = null): array
    {
        $employers = Employer::findAll();
        $gqlTypes = [];

        foreach($employers as $employer) {
            $requiredContexts = EmployerElement::gqlScopesByContext($employer);

            if(!GqlHelper::isSchemaAwareOf($requiredContexts)) {
                continue;
            }

            // Generate a type for each employer
            $type = static::generateType($employer);
            $gqlTypes[$type->name] = $type;
        }

        return $gqlTypes;
    }

    public static function generateType($context): ObjectType
    {
        $typeName = EmployerElement::gqlTypeNameByContext($context);
        $contentFieldGqlTypes = self::getContentFields($context);

        $employerFields = TypeManager::prepareFieldDefinitions(array_merge(EmployerInterface::getFieldDefinitions(), $contentFieldGqlTypes), $typeName);

        return GqlEntityRegistry::getEntity($typeName) ?: GqlEntityRegistry::createEntity($typeName, new EmployerElementType([
            'name' => $typeName,
            'fields' => function() use ($employerFields) {
                return $employerFields;
            },
        ]));
    }
}
