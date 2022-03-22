<?php

namespace percipiolondon\staff\gql\types;

use craft\gql\types\DateTime;

use percipiolondon\staff\gql\base\GqlTypeTrait;
use GraphQL\Type\Definition\Type;


/**
 * Class LeaveSettings
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class LeaveSettings
{
    use GqlTypeTrait;

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'leaveSettings';
    }

    /**
     * List of fields for this type.
     *
     * @return array
     */
    public static function getFieldDefinitions(): array
    {
        return [
            'useDefaultHolidayType' => [
                'name' => 'useDefaultHolidayType',
                'type' => Type::boolean(),
            ],
            'useDefaultAllowanceResetDate' => [
                'name' => 'useDefaultAllowanceResetDate',
                'type' => Type::boolean(),
            ],
            'useDefaultAllowance' => [
                'name' => 'useDefaultAllowance',
                'type' => Type::boolean(),
            ],
            'useDefaultAccruePaymentInLieu' => [
                'name' => 'useDefaultAccruePaymentInLieu',
                'type' => Type::boolean(),
            ],
            'useDefaultAccruePaymentInLieuRate' => [
                'name' => 'useDefaultAccruePaymentInLieuRate',
                'type' => Type::boolean(),
            ],
            'useDefaultAccruePaymentInLieuAllGrossPay' => [
                'name' => 'useDefaultAccruePaymentInLieuAllGrossPay',
                'type' => Type::boolean(),
            ],
            'useDefaultAccruePaymentInLieuPayAutomatically' => [
                'name' => 'useDefaultAccruePaymentInLieuPayAutomatically',
                'type' => Type::boolean(),
            ],
            'useDefaultAccrueHoursPerDay' => [
                'name' => 'useDefaultAccrueHoursPerDay',
                'type' => Type::boolean(),
            ],
            'allowanceResetDate' => [
                'name' => 'allowanceResetDate',
                'type' => DateTime::getType(),
            ],
            'allowance' => [
                'name' => 'allowance',
                'type' => Type::float(),
            ],
            'adjustment' => [
                'name' => 'adjustment',
                'type' => Type::float(),
            ],
            'allowanceUsed' => [
                'name' => 'allowanceUsed',
                'type' => Type::float(),
            ],
            'allowanceUsedPreviousPeriod' => [
                'name' => 'allowanceUsedPreviousPeriod',
                'type' => Type::float(),
            ],
            'allowanceRemaining' => [
                'name' => 'allowanceRemaining',
                'type' => Type::float(),
            ],
            // TODO CREATE ENUM
            'holidayType' => [
                'name' => 'holidayType',
                'type' => Type::string(),
            ],
            'accrueSetAmount' => [
                'name' => 'accrueSetAmount',
                'type' => Type::boolean(),
            ],
            'accrueHoursPerDay' => [
                'name' => 'accrueHoursPerDay',
                'type' => Type::float(),
            ],
            'showAllowanceOnPayslip' => [
                'name' => 'showAllowanceOnPayslip',
                'type' => Type::boolean(),
            ],
            'showAhpOnPayslip' => [
                'name' => 'showAhpOnPayslip',
                'type' => Type::boolean(),
            ],
            'accruePaymentInLieuRate' => [
                'name' => 'accruePaymentInLieuRate',
                'type' => Type::float(),
            ],
            'accruePaymentInLieuAllGrossPay' => [
                'name' => 'accruePaymentInLieuAllGrossPay',
                'type' => Type::boolean(),
            ],
            'accruePaymentInLieuPayAutomatically' => [
                'name' => 'accruePaymentInLieuPayAutomatically',
                'type' => Type::boolean(),
            ],
            'accruedPaymentLiability' => [
                'name' => 'accruedPaymentLiability',
                'type' => Type::float(),
            ],
            'accruedPaymentAdjustment' => [
                'name' => 'accruedPaymentLiability',
                'type' => Type::float(),
            ],
            'accruedPaymentPaid' => [
                'name' => 'accruedPaymentLiability',
                'type' => Type::float(),
            ],
            'accruedPaymentBalance' => [
                'name' => 'accruedPaymentLiability',
                'type' => Type::float(),
            ],
        ];
    }

}
