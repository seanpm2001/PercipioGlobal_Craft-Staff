<?php

namespace percipiolondon\staff\gql\types;

use craft\gql\base\GqlTypeTrait;
use craft\gql\types\DateTime;

use GraphQL\Type\Definition\Type;

/**
 * Class LeaverDetails
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class LeaverDetails
{
    use GqlTypeTrait;

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'leaverDetails';
    }

    /**
     * List of fields for this type.
     *
     * @return array
     */
    public static function getFieldDefinitions(): array
    {
        return [
            'hasLeft' => [
                'name' => 'hasLeft',
                'type' => Type::boolean(),
            ],
            'leaveDate' => [
                'name' => 'leaveDate',
                'type' => DateTime::getType(),
                'description' => 'Leave date of directorship.',
            ],
            'isDeceased' => [
                'name' => 'isDeceased',
                'type' => Type::boolean(),
            ],
            'paymentAfterLeaving' => [
                'name' => 'paymentAfterLeaving',
                'type' => Type::boolean(),
            ],
            'p45Sent' => [
                'name' => 'p45Sent',
                'type' => Type::boolean(),
            ],
        ];
    }

}
