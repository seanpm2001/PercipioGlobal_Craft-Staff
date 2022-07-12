<?php

namespace percipiolondon\staff\gql\interfaces\elements;

use craft\gql\GqlEntityRegistry;
use craft\gql\interfaces\Element;
use craft\gql\TypeManager;
use craft\gql\types\DateTime;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;
use percipiolondon\staff\elements\Employer as EmployerElement;
use percipiolondon\staff\gql\types\generators\EmployerGenerator;
use percipiolondon\staff\gql\types\generators\RequestGenerator;

class Request extends Element
{
    public static function getTypeGenerator(): string
    {
        return RequestGenerator::class;
    }

    /**
     * @inheritdoc
     */
    public static function getType($fields = null): Type
    {
        if ($type = GqlEntityRegistry::getEntity(self::getName())) {
            return $type;
        }

        $type = GqlEntityRegistry::createEntity(self::getName(), new InterfaceType([
            'name' => static::getName(),
            'fields' => self::class . '::getFieldDefinitions',
            'description' => 'This is the interface implemented by all employers.',
            'resolveType' => function(EmployerElement $value) {
                return $value->getGqlTypeName();
            },
        ]));

        EmployerGenerator::generateTypes();

        return $type;
    }

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'RequestInterface';
    }

    /**
     * @inheritdoc
     */
    public static function getFieldDefinitions(): array
    {
        $parentFields = parent::getFieldDefinitions();
        unset($parentFields['slug']);

        $fields = [
            'dateAdministered' => [
                'name' => 'dateAdministered',
                'type' => DateTime::getType(),
                'description' => 'Date administered',
            ],
            'employerId' => [
                'name' => 'employerId',
                'type' => Type::int(),
                'description' => 'Employer id',
            ],
            'employeeId' => [
                'name' => 'employeeId',
                'type' => Type::int(),
                'description' => 'Employee id',
            ],
            'administerId' => [
                'name' => 'administerId',
                'type' => Type::int(),
                'description' => 'Administer id',
            ],
            'data' => [
                'name' => 'data',
                'type' => Type::string(),
                'description' => 'Data',
            ],
            'type' => [
                'name' => 'type',
                'type' => Type::string(),
                'description' => 'Type',
            ],
            'status' => [
                'name' => 'status',
                'type' => Type::string(),
                'description' => 'Status',
            ],
        ];

        return TypeManager::prepareFieldDefinitions(array_merge($parentFields, $fields), self::getName());
    }
}