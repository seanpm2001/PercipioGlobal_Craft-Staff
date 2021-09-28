<?php

namespace percipiolondon\craftstaff\gql\interfaces\elements;

use craft\gql\GqlEntityRegistry;
use craft\gql\interfaces\Element;
use craft\gql\TypeManager;
use craft\gql\types\DateTime;

use percipiolondon\craftstaff\gql\types\BankDetails;
use percipiolondon\craftstaff\gql\types\EmploymentDetails;
use percipiolondon\craftstaff\gql\types\LeaveSettings;
use percipiolondon\craftstaff\gql\types\PayOptions;
use percipiolondon\craftstaff\gql\types\PersonalDetails;
use percipiolondon\craftstaff\gql\types\RightToWork;
use percipiolondon\craftstaff\gql\types\generators\EmployeeType;

use craft\helpers\Gql;
use craft\helpers\Json;

use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;

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
            'personalDetails' => [
                'name' => 'personalDetails',
                'type' => PersonalDetails::getType(),
            ],
            'employmentDetails' => [
                'name' => 'employmentDetails',
                'type' => EmploymentDetails::getType(),
            ],
            'leaveSettings' => [
                'name' => 'leaveSettings',
                'type' => LeaveSettings::getType(),
            ],
            'rightToWork' => [
                'name' => 'rightToWork',
                'type' => RightToWork::getType(),
            ],
            'bankDetails' => [
                'name' => 'bankDetails',
                'type' => BankDetails::getType(),
            ],
            'payOptions' => [
                'name' => 'payOptions',
                'type' => PayOptions::getType(),
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