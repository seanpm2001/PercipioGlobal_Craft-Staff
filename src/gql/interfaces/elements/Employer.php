<?php

namespace percipiolondon\craftstaff\gql\interfaces\elements;

use craft\gql\interfaces\Element;
use craft\gql\TypeManager;
use craft\gql\types\DateTime;
use GraphQL\Type\Definition\Type;
use percipiolondon\craftstaff\elements\Employer as EmployerElement;

class Employer extends Element
{
    /**
     * @inheritdoc
     */
    public static function getTypeGenerator(): string
    {
        return EmployerType::class;
    }

    public static function getType($fields = null): Type
    {
        return new \GraphQL\Type\Definition\ObjectType([
            'name' => 'employerData',
            'fields' => [
                'name' => [
                    'name' => 'name',
                    'type' => Type::string(),
                    'resolve' => function($rootValue) {
                        $employer = EmployerElement::findOne($rootValue);
                        return $employer->name ?? '';
                    }
                ],
                'crn' => [
                    'name' => 'crn',
                    'type' => Type::string(),
                    'resolve' => function($rootValue) {
                        $employer = EmployerElement::findOne($rootValue);
                        return $employer->crn ?? '';
                    }
                ],
                // Custom Resolver --> addressObjectType
                'address' => [
                    'name' => 'address',
                    'type' => Type::string(),
                    'resolve' => function($rootValue) {
                        $employer = EmployerElement::findOne($rootValue);
                        return $employer->address ?? '';
                    }
                ],
                // Custom Resolver --> hmrcObjectType
                'hmrcDetails' => [
                    'name' => 'hmrcDetails',
                    'type' => Type::string(),
                    'resolve' => function($rootValue) {
                        $employer = EmployerElement::findOne($rootValue);
                        return $employer->hmrcDetails ?? '';
                    }
                ],
                'startYear' => [
                    'name' => 'startYear',
                    'type' => Type::string(),
                    'resolve' => function($rootValue) {
                        $employer = EmployerElement::findOne($rootValue);
                        return $employer->startYear ?? '';
                    }
                ],
                'currentYear' => [
                    'name' => 'currentYear',
                    'type' => Type::string(),
                    'resolve' => function($rootValue) {
                        $employer = EmployerElement::findOne($rootValue);
                        return $employer->currentYear ?? '';
                    }
                ],
                'employeeCount' => [
                    'name' => 'employeeCount',
                    'type' => Type::int(),
                    'resolve' => function($rootValue) {
                        $employer = EmployerElement::findOne($rootValue);
                        return $employer->employeeCount ?? '';
                    }
                ],
                // Custom Resolver --> payOptionsObjectType
                'defaultPayOptions' => [
                    'name' => 'defaultPayOptions',
                    'type' => Type::string(),
                    'resolve' => function($rootValue) {
                        $employer = EmployerElement::findOne($rootValue);
                        return $employer->defaultPayOptions ?? '';
                    }
                ],
            ],
        ]);
    }

    public static function getName(): string
    {
        return 'EmployerInterface';
    }

    public static function getFieldDefinitions(): array
    {
        return TypeManager::prepareFieldDefinitions(array_merge(parent::getFieldDefinitions(), self::getConditionalFields(), [
            'name' => [
                'name' => 'name',
                'type' => Type::int(),
                'description' => 'The name of the employer'
            ],
            'crn' => [
                'name' => 'crn',
                'type' => Type::string(),
            ],
            'address' => [
                'name' => 'address',
                'type' => Type::string(),
                'description' => "The employer's address"
            ],
            'hmrcDetails' => [
                'name' => 'hmrcDetails',
                'type' => Type::string(),
                'description' => "The employer's hmrc details"
            ],
            'startYear' => [
                'name' => 'startYear',
                'type' => Type::string(),
            ],
            'currentYear' => [
                'name' => 'currentYear',
                'type' => Type::string(),
            ],
            'employeeCount' => [
                'name' => 'employeeCount',
                'type' => Type::int(),
            ],
            'defaultPayOptions' => [
                'name' => 'defaultPayOptions',
                'type' => Type::string(),
            ],
        ]), self::getName());
    }

    protected static function getConditionalFields(): array
    {
        return [];
    }
}
