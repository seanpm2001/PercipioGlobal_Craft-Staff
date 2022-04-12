<?php

namespace percipiolondon\staff\gql\types;

use GraphQL\Type\Definition\Type;

use percipiolondon\staff\gql\base\GqlTypeTrait;

/**
 * Class NationalInsuranceCalculation
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class NationalInsuranceCalculation
{
    use GqlTypeTrait;

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'nationalInsuranceCalculation';
    }

    /**
     * List of fields for this type.
     *
     * @return array
     */
    public static function getFieldDefinitions(): array
    {
        return [
            'earningsUptoIncludingLEL' => [
                'name' => 'earningsUptoIncludingLEL',
                'type' => Type::float(),
            ],
            'earningsAboveLELUptoIncludingPT' => [
                'name' => 'earningsAboveLELUptoIncludingPT',
                'type' => Type::float(),
            ],
            'earningsAbovePTUptoIncludingST' => [
                'name' => 'earningsAbovePTUptoIncludingST',
                'type' => Type::float(),
            ],
            'earningsAbovePTUptoIncludingUEL' => [
                'name' => 'earningsAbovePTUptoIncludingUEL',
                'type' => Type::float(),
            ],
            'earningsAboveSTUptoIncludingUEL' => [
                'name' => 'earningsAboveSTUptoIncludingUEL',
                'type' => Type::float(),
            ],
            'earningsAboveUEL' => [
                'name' => 'earningsAboveUEL',
                'type' => Type::float(),
            ],
            'employeeNiGross' => [
                'name' => 'employeeNiGross',
                'type' => Type::float(),
                'description' => 'Employee National Insurance Gross Value',
            ],
            'employeeNiRebate' => [
                'name' => 'employeeNiRebate',
                'type' => Type::float(),
                'description' => 'Employee National Insurance Gross Value',
            ],
            'employerNiGross' => [
                'name' => 'employerNiGross',
                'type' => Type::float(),
                'description' => 'Employer National Insurance Gross Value',
            ],
            'employerNiRebate' => [
                'name' => 'employerNiRebate',
                'type' => Type::float(),
                'description' => 'Employer National Insurance Gross Value',
            ],
            'employeeNi' => [
                'name' => 'employeeNi',
                'type' => Type::float(),
                'description' => 'Net Employee National Insurance',
            ],
            'employerNi' => [
                'name' => 'employerNi',
                'type' => Type::float(),
                'description' => 'Net Employer National Insurance',
            ],
            'netNi' => [
                'name' => 'employerNiRebate',
                'type' => Type::float(),
                'description' => 'Net National Insurance (Employer + Employee)',
            ],
        ];
    }
}
