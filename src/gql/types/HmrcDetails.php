<?php

namespace percipiolondon\staff\gql\types;

use GraphQL\Type\Definition\Type;
use percipiolondon\staff\gql\base\GqlTypeTrait;

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
            ],
            'apprenticeshipLevyAllowance' => [
                'name' => 'apprenticeshipLevyAllowance',
                'type' => Type::string(),
                'description' => 'Apprenticeship Levy Allowance',
            ],
            'payeReference' => [
                'name' => 'payeReference',
                'type' => Type::string(),
                'description' => 'PAYE Reference.',
            ],
            'accountsOfficeReference' => [
                'name' => 'accountsOfficeReference',
                'type' => Type::string(),
                'description' => 'Accounts office reference.',
            ],
            'employmentAllowance' => [
                'name' => 'employmentAllowance',
                'type' => Type::boolean(),
                'description' => 'Employment allowance.',
            ],
            'employmentAllowanceMaxClaim' => [
                'name' => 'employmentAllowanceMaxClaim',
                'type' => Type::int(),
                'description' => 'Employment allowance max claim.',
            ],
            'quarterlyPaymentSchedule' => [
                'name' => 'quarterlyPaymentSchedule',
                'type' => Type::boolean(),
                'description' => 'Quarterly payment schedule.',
            ],
            'includeEmploymentAllowanceOnMonthlyJournal' => [
                'name' => 'includeEmploymentAllowanceOnMonthlyJournal',
                'type' => Type::boolean(),
                'description' => 'Include employment allowance on monthly journal.',
            ],
            'carryForwardUnpaidLiabilities' => [
                'name' => 'carryForwardUnpaidLiabilities',
                'type' => Type::boolean(),
                'description' => 'Carry forward unpaid liabilities.',
            ],
            'id' => [
                'name' => 'staffologyId',
                'type' => Type::string(),
                'description' => 'Staffology employer ID.',
            ],
        ];
    }
}
