<?php

namespace percipiolondon\craftstaff\gql\types;

use GraphQL\Type\Definition\Type;

use percipiolondon\craftstaff\gql\base\GqlTypeTrait;


/**
 * Class ValueOverride
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class ValueOverride
{
    use GqlTypeTrait;

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'valueOverride';
    }

    /**
     * List of fields for this type.
     *
     * @return array
     */
    public static function getFieldDefinitions(): array
    {
        return [
            // TODO CREATE ENUM
            'type' => [
                'name' => 'type',
                'type' => Type::string(),
            ],
            'value' => [
                'name' => 'value',
                'type' => Type::float(),
                'description' => 'The value to use in place of the original value',
            ],
            'originalValue' => [
                'name' => 'originalValue',
                'type' => Type::float(),
                'description' => 'The original value',
            ],
            'note' => [
                'name' => 'note',
                'type' => Type::string(),
                'description' => 'The reason given for the override',
            ],
            'attachmentOrderId' => [
                'name' => 'attachmentOrderId',
                'type' => Type::string(),
                'description' => 'The Id of the AttachmentOrder. Only relevant if the Type is set to AttachmentOrderDeductions',
            ],
            'pensionId' => [
                'name' => 'pensionId',
                'type' => Type::string(),
                'description' => 'The Id of the associated Pension. Only included if the Code is PENSION, PENSIONSS or PENSIONRAS',
            ],
        ];
    }

}
