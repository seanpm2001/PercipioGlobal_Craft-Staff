<?php

namespace percipiolondon\staff\gql\interfaces\elements;

use craft\gql\GqlEntityRegistry;
use craft\gql\interfaces\Element;
use craft\gql\TypeManager;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use percipiolondon\staff\elements\Notification as NotificationElement;
use percipiolondon\staff\gql\types\Employee;
use percipiolondon\staff\gql\types\generators\NotificationGenerator;

class Notification extends Element
{
    public static function getTypeGenerator(): string
    {
        return NotificationGenerator::class;
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
            'description' => 'This is the interface implemented by all requests.',
            'resolveType' => function(NotificationElement $value) {
                return $value->getGqlTypeName();
            },
        ]));

        NotificationGenerator::generateTypes();

        return $type;
    }

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'NotificationInterface';
    }

    /**
     * @inheritdoc
     */
    public static function getFieldDefinitions(): array
    {
        $parentFields = parent::getFieldDefinitions();

        $fields = [
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
            'message' => [
                'name' => 'message',
                'type' => Type::string(),
                'description' => 'Message',
                'resolve' => function ($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    //pass the translation 'en' (hardcoded, can be an argument in the future)
                    return \Craft::t('staff-management', $source->message, 'en');
                }
            ],
            'type' => [
                'name' => 'type',
                'type' => Type::string(),
                'description' => 'Type',
            ],
            'viewed' => [
                'name' => 'viewed',
                'type' => Type::boolean(),
                'description' => 'Viewed',
            ],
            'employee' => [
                'name' => 'employee',
                'type' => Employee::getType(),
                'description' => 'The employee of where the request belongs to'
            ],
            'employer' => [
                'name' => 'employer',
                'type' => Type::string(),
                'description' => 'The company name of where the pay run belongs to',
            ]
        ];

        return TypeManager::prepareFieldDefinitions(array_merge($parentFields, $fields), self::getName());
    }
}