<?php

namespace percipiolondon\staff\gql\interfaces\elements;

use craft\gql\interfaces\Element;
use craft\gql\GqlEntityRegistry;
use craft\gql\TypeManager;

use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;

use percipiolondon\staff\elements\Employee as EmployeeElement;
use percipiolondon\staff\gql\types\EmploymentDetails;
use percipiolondon\staff\gql\types\generators\EmployeeGenerator;
use percipiolondon\staff\gql\types\LeaveSettings;
use percipiolondon\staff\gql\types\PersonalDetails;
use percipiolondon\staff\helpers\Security as SecurityHelper;

/**
 * Class Employee
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class Employee extends Element
{
    /**
     * @inheritdoc
     */
    public static function getTypeGenerator(): string
    {
        return EmployeeGenerator::class;
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
            'description' => 'This is the interface implemented by all employees.',
            'resolveType' => function(EmployeeElement $value) {
                return $value->getGqlTypeName();
            }
        ]));

        EmployeeGenerator::generateTypes();

        return $type;
    }

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'EmployeeInterface';
    }

    /**
     * @inheritdoc
     */
    public static function getFieldDefinitions(): array
    {
        $parentFields = parent::getFieldDefinitions();
        unset($parentFields['slug']);

        $securedFields = [
            'niNumber' => [
                'name' => 'niNumber',
                'type' => Type::id(),
                'description' => 'Nation insurance number.',
                'resolve' => function ($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return SecurityHelper::resolve($source, $resolveInfo);
                }
            ],
            'slug' => [
                'name' => 'slug',
                'type' => Type::nonNull(Type::string()),
                'description' => 'The company slug.',
                'resolve' => function ($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return SecurityHelper::resolve($source, $resolveInfo);
                }
            ],
        ];

        $fields = [
            'staffologyId' => [
                'name' => 'staffologyId',
                'type' => Type::nonNull(Type::id()),
                'description' => 'The employee id from staffology, needed for API calls.'
            ],
            'employerId' => [
                'name' => 'employerId',
                'type' => Type::id(),
                'description' => 'The id of the employer this employee works for.',
            ],
            'userId' => [
                'name' => 'userId',
                'type' => Type::id(),
                'description' => 'The user ID.',
            ],
            'status' => [
                'name' => 'status',
                'type' => Type::string(),
                'description' => 'The employee status.'
            ],
            'isDirector' => [
                'name' => 'isDirector',
                'type' => Type::boolean(),
                'description' => 'Is this employer a employer'
            ],
            'employmentDetails' => [
                'name' => 'employmentDetails',
                'type' => EmploymentDetails::getType(),
                'description' => 'The employment details info of an employee'
            ],
            'leaveSettings' => [
                'name' => 'leaveSettings',
                'type' => LeaveSettings::getType(),
                'description' => 'The leave setting details info of an employee'
            ],
            'personalDetails' => [
                'name' => 'personalDetails',
                'type' => PersonalDetails::getType(),
                'description' => 'The personal details of an employee'
            ]

        ];

        return TypeManager::prepareFieldDefinitions(array_merge($parentFields, $securedFields, $fields), self::getName());
    }

}
