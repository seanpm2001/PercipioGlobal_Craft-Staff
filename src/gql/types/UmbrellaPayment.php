<?php

namespace percipiolondon\staff\gql\types;

use percipiolondon\staff\gql\base\GqlTypeTrait;
use GraphQL\Type\Definition\Type;


/**
 * Class UmbrellaPayment
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class UmbrellaPayment
{
    use GqlTypeTrait;

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'umbrellaPayment';
    }

    /**
     * List of fields for this type.
     *
     * @return array
     */
    public static function getFieldDefinitions(): array
    {
        return [
            'payrollCode' => [
                'name' => 'payrollCode',
                'type' => Type::string(),
                'description' => 'When importing multiple UmbrellaPayments this field is used to identify the employee',
            ],
            'chargePerTimesheet' => [
                'name' => 'chargePerTimesheet',
                'type' => Type::float(),
            ],
            'invoiceValue' => [
                'name' => 'invoiceValue',
                'type' => Type::float(),
            ],
            'mapsMiles' => [
                'name' => 'mapsMiles',
                'type' => Type::int(),
            ],
            'otherExpenses' => [
                'name' => 'otherExpenses',
                'type' => Type::float(),
            ],
            'numberOfTimesheets' => [
                'name' => 'numberOfTimesheets',
                'type' => Type::int(),
            ],
            'hoursWorked' => [
                'name' => 'hoursWorked',
                'type' => Type::float(),
            ],
        ];
    }

}
