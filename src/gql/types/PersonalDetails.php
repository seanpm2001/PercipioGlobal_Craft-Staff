<?php

namespace percipiolondon\staff\gql\types;

use craft\gql\base\GqlTypeTrait;
use craft\gql\types\DateTime;
use GraphQL\Type\Definition\Type;

/**
 * Class PersonalDetails
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class PersonalDetails
{
    use GqlTypeTrait;

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'personalDetails';
    }

    /**
     * List of fields for this type.
     *
     * @return array
     */
    public static function getFieldDefinitions(): array
    {
        return [
            'address' => [
                'name' => 'address',
                'type' => Address::getType(),
                'description' => 'The address.',
            ],
            // TODO create Enum
            'maritalStatus' => [
                'name' => 'maritalStatus',
                'type' => Type::string(),
                'description' => 'Marital Status.',
            ],
            'title' => [
                'name' => 'title',
                'type' => Type::string(),
                'description' => 'Name title.',
            ],
            'firstName' => [
                'name' => 'firstName',
                'type' => Type::string(),
                'description' => 'First name.',
            ],
            'middleName' => [
                'name' => 'middleName',
                'type' => Type::string(),
                'description' => 'Middle name.',
            ],
            'lastName' => [
                'name' => 'lastName',
                'type' => Type::string(),
                'description' => 'Last name.',
            ],
            'email' => [
                'name' => 'email',
                'type' => Type::string(),
                'description' => 'Email address.',
            ],
            'emailPayslip' => [
                'name' => 'emailPayslip',
                'type' => Type::boolean(),
                'description' => 'Does the employee want to receive payslips through email?',
            ],
            'telephone' => [
                'name' => 'telephone',
                'type' => Type::string(),
                'description' => 'Telephone number.',
            ],
            'mobile' => [
                'name' => 'mobile',
                'type' => Type::string(),
                'description' => 'Mobile number.',
            ],
            'dateOfBirth' => [
                'name' => 'dateOfBirth',
                'type' => DateTime::getType(),
                'description' => 'Mobile number.',
            ],
            'statePensionAge' => [
                'name' => 'statePensionAge',
                'type' => Type::int(),
                'description' => 'Mobile number.',
            ],
            'niNumber' => [
                'name' => 'niNumber',
                'type' => Type::string(),
                'description' => 'National insurance number.',
            ],
            'passportNumber' => [
                'name' => 'passportNumber',
                'type' => Type::string(),
                'description' => 'Passport number.',
            ],
            'partnerDetails' => [
                'name' => 'partnerDetails',
                'type' => PartnerDetails::getType(),
                'description' => 'Details of the partner.',
            ],
        ];
    }
}
