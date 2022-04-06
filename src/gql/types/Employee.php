<?php

namespace percipiolondon\staff\gql\types;

use craft\gql\base\GqlTypeTrait;
use GraphQL\Type\Definition\Type;


/**
 * Class Employee
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class Employee
{
    use GqlTypeTrait;

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'employee';
    }

    /**
     * List of fields for this type.
     *
     * @return array
     */
    public static function getFieldDefinitions(): array
    {
        return [
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
    }

}
