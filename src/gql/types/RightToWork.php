<?php

namespace percipiolondon\staff\gql\types;

use percipiolondon\staff\gql\base\GqlTypeTrait;
use GraphQL\Type\Definition\Type;


/**
 * Class RightToWork
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class RightToWork
{
    use GqlTypeTrait;

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'rightToWork';
    }

    /**
     * List of fields for this type.
     *
     * @return array
     */
    public static function getFieldDefinitions(): array
    {
        return [
            'checked' => [
                'name' => 'checked',
                'type' => Type::boolean(),
            ],
            'documentType' => [
                'name' => 'documentType',
                'type' => Type::string(),
            ],
            'documentRef' => [
                'name' => 'documentRef',
                'type' => Type::string(),
            ],
            'documentExpiry' => [
                'name' => 'documentExpiry',
                'type' => Type::string(),
            ],
            'note' => [
                'name' => 'note',
                'type' => Type::string(),
            ],
        ];
    }

}
