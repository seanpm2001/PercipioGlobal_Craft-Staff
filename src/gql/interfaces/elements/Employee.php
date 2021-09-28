<?php

namespace percipiolondon\craftstaff\gql\interfaces\elements;

use craft\gql\GqlEntityRegistry;
use craft\gql\interfaces\Element;
use craft\gql\TypeManager;
use craft\gql\types\DateTime;

use percipiolondon\craftstaff\gql\types\generators\EmployeeType;

use craft\helpers\Gql;
use craft\helpers\Json;

use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;

class Employee extends Element
{
    /**
     * @inheritdoc
     */
    public static function getTypeGenerator(): string
    {
        return EmployeeType::class;
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
            'resolveType' => self::class . '::resolveElementTypeName',
        ]));

        EmployeeType::generateTypes();

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

        //public $slug;
        //public $siteId;
        //public $staffologyId;
        //public $employerId;
        //public $userId;
        //public $personalDetails;
        //public $employmentDetails;
        //public $autoEnrolment;
        //public $leaveSettings;
        //public $rightToWork;
        //public $bankDetails;
        //public $status;
        //public $aeNotEnroledWarning;
        //public $niNumber;
        //public $sourceSystemId;

        return TypeManager::prepareFieldDefinitions(array_merge(parent::getFieldDefinitions(), self::getConditionalFields(), [
            'staffologyId' => [
                'name' => 'staffologyId',
                'type' => Type::string(),
                'description' => 'The employee id from staffology, needed for API calls.'
            ],
            'employerId' => [
                'name' => 'employerId',
                'type' => Type::int(),
                'description' => 'The id of the employer this employee works for.',
            ],
            'userId' => [
                'name' => 'userId',
                'type' => Type::int(),
                'description' => 'The user ID.',
            ],
            'rightToWork' => [
                'name' => 'rightToWork',
                'type' => Type::boolean(),
                'description' => 'Has the employee the right to work?',
            ],
            // TODO: Create Enum
            'status' => [
                'name' => 'status',
                'type' => Type::string(),
                'description' => 'The employee status.'
            ],
            'aeNotEnroledWarning' => [
                'name' => 'aeNotEnroledWarning',
                'type' => Type::boolean(),
                'description' => 'AE not enroled warning.'
            ],
            'niNumber' => [
                'name' => 'niNumber',
                'type' => Type::string(),
                'description' => 'National insurance number.'
            ],
            'sourceSystemId' => [
                'name' => 'sourceSystemId',
                'type' => Type::string(),
                'description' => 'Source system ID.'
            ],

        ]), self::getName());
    }

    /**
     * @inheritdoc
     */
    protected static function getConditionalFields(): array
    {
        return [];
    }
}