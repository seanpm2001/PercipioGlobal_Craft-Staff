<?php

namespace percipiolondon\staff\gql\types;

use craft\gql\base\GqlTypeTrait;
use craft\gql\types\DateTime;

use craft\helpers\DateTimeHelper;
use craft\helpers\Gql;
use GraphQL\Type\Definition\ResolveInfo;
use percipiolondon\staff\gql\types\Address;
use percipiolondon\staff\gql\types\PartnerDetails;
use GraphQL\Type\Definition\Type;
use percipiolondon\staff\helpers\Security;

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
                'resolve' => function ($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return Security::resolve($source, $resolveInfo, 'string');
                },
            ],
            'emailPayslip' => [
                'name' => 'emailPayslip',
                'type' => Type::boolean(),
                'description' => 'Does the employee want to receive payslips through email?',
                'resolve' => function ($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return Security::resolve($source, $resolveInfo, 'string');
                },
            ],
            'telephone' => [
                'name' => 'telephone',
                'type' => Type::string(),
                'description' => 'Telephone number.',
                'resolve' => function ($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return Security::resolve($source, $resolveInfo, 'string');
                },
            ],
            'mobile' => [
                'name' => 'mobile',
                'type' => Type::string(),
                'description' => 'Mobile number.',
                'resolve' => function ($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return Security::resolve($source, $resolveInfo, 'string');
                },
            ],
            'dateOfBirth' => [
                'name' => 'dateOfBirth',
                'type' => DateTime::getType(),
                'description' => 'Mobile number.',
                'resolve' => function ($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return Gql::applyDirectives($source, $resolveInfo, DateTimeHelper::toDateTime($source['dateOfBirth']));
                }
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
                'resolve' => function ($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return Security::resolve($source, $resolveInfo, 'string');
                },
            ],
            'passportNumber' => [
                'name' => 'passportNumber',
                'type' => Type::string(),
                'description' => 'Passport number.',
                'resolve' => function ($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return Security::resolve($source, $resolveInfo, 'string');
                },
            ],
            'partnerDetails' => [
                'name' => 'partnerDetails',
                'type' => PartnerDetails::getType(),
                'description' => 'Details of the partner.',
            ],
        ];
    }
}
