<?php

namespace percipiolondon\craftstaff\gql\types;

use percipiolondon\craftstaff\gql\base\GqlTypeTrait;
use GraphQL\Type\Definition\Type;


/**
 * Class BankDetails
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class BankDetails
{
    use GqlTypeTrait;

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'bankDetails';
    }

    /**
     * List of fields for this type.
     *
     * @return array
     */
    public static function getFieldDefinitions(): array
    {
        return [
            'bankeName' => [
                'name' => 'bankName',
                'type' => Type::string(),
            ],
            'bankBranch' => [
                'name' => 'bankBranch',
                'type' => Type::string(),
            ],
            'bankReference' => [
                'name' => 'bankReference',
                'type' => Type::string(),
            ],
            'accountName' => [
                'name' => 'accountName',
                'type' => Type::string(),
            ],
            'accountNumber' => [
                'name' => 'accountNumber',
                'type' => Type::string(),
            ],
            'sortCode' => [
                'name' => 'sortCode',
                'type' => Type::string(),
            ],
            'note' => [
                'name' => 'note',
                'type' => Type::string(),
            ],
        ];
    }

}
