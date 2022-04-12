<?php

namespace percipiolondon\staff\gql\types;

use craft\gql\base\GqlTypeTrait;
use GraphQL\Type\Definition\Type;

/**
 * Class Address
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class TaxAndNi
{
    use GqlTypeTrait;

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'taxAndNi';
    }

    /**
     * List of fields for this type.
     *
     * @return array
     */
    public static function getFieldDefinitions(): array
    {
        return [
            'niTable' => [
                'name' => 'niTable',
                'type' => Type::String(),
                'description' => 'National Insurance Table',
            ],
            'secondaryClass1NotPayable' => [
                'name' => 'secondaryClass1NotPayable',
                'type' => Type::boolean(),
                'description' => 'Secondary class not payable?',
            ],
            'postgradLoan' => [
                'name' => 'postgradLoan',
                'type' => Type::boolean(),
                'description' => 'Postgraduate Loan?',
            ],
            'studentLoan' => [
                'name' => 'studentLoan',
                'type' => Type::String(),
                'description' => 'Student Loan.',
            ],
            'taxCode' => [
                'name' => 'taxCode',
                'type' => Type::String(),
                'description' => 'Tax code.',
            ],
            'week1Month1' => [
                'name' => 'week1Month1',
                'type' => Type::boolean(),
                'description' => 'Week 1, Month 1?',
            ],
        ];
    }
}
