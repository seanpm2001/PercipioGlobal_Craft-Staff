<?php

namespace percipiolondon\staff\gql\types;

use craft\gql\base\GqlTypeTrait;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use percipiolondon\staff\helpers\Security;

/**
 * Class Address
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class HmrcDetails
{
    use GqlTypeTrait;

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'hmrcDetails';
    }

    /**
     * List of fields for this type.
     *
     * @return array
     */
    public static function getFieldDefinitions(): array
    {
        return [
            'officeNumber' => [
                'name' => 'officeNumber',
                'type' => Type::string(),
                'description' => 'Office Number.',
                'resolve' => function ($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return Security::resolve($source, $resolveInfo);
                },
            ],
            'apprenticeshipLevyAllowance' => [
                'name' => 'apprenticeshipLevyAllowance',
                'type' => Type::string(),
                'description' => 'Apprenticeship Levy Allowance',
                'resolve' => function ($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return Security::resolve($source, $resolveInfo);
                },
            ],
            'payeReference' => [
                'name' => 'payeReference',
                'type' => Type::string(),
                'description' => 'PAYE Reference.',
                'resolve' => function ($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return Security::resolve($source, $resolveInfo);
                },
            ],
            'accountsOfficeReference' => [
                'name' => 'accountsOfficeReference',
                'type' => Type::string(),
                'description' => 'Accounts office reference.',
                'resolve' => function ($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return Security::resolve($source, $resolveInfo);
                },
            ],
            'employmentAllowance' => [
                'name' => 'employmentAllowance',
                'type' => Type::boolean(),
                'description' => 'Employment allowance.',
                'resolve' => function ($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return Security::resolve($source, $resolveInfo, 'boolean');
                },
            ],
            'employmentAllowanceMaxClaim' => [
                'name' => 'employmentAllowanceMaxClaim',
                'type' => Type::int(),
                'description' => 'Employment allowance max claim.',
                'resolve' => function ($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return Security::resolve($source, $resolveInfo, 'int');
                },
            ],
            'quarterlyPaymentSchedule' => [
                'name' => 'quarterlyPaymentSchedule',
                'type' => Type::boolean(),
                'description' => 'Quarterly payment schedule.',
                'resolve' => function ($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return Security::resolve($source, $resolveInfo, 'boolean');
                },
            ],
            'includeEmploymentAllowanceOnMonthlyJournal' => [
                'name' => 'includeEmploymentAllowanceOnMonthlyJournal',
                'type' => Type::boolean(),
                'description' => 'Include employment allowance on monthly journal.',
                'resolve' => function ($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return Security::resolve($source, $resolveInfo, 'boolean');
                },
            ],
            'carryForwardUnpaidLiabilities' => [
                'name' => 'carryForwardUnpaidLiabilities',
                'type' => Type::boolean(),
                'description' => 'Carry forward unpaid liabilities.',
                'resolve' => function ($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return Security::resolve($source, $resolveInfo, 'boolean');
                },
            ],
            'id' => [
                'name' => 'staffologyId',
                'type' => Type::string(),
                'description' => 'Staffology employer ID.',
            ],
        ];
    }
}
