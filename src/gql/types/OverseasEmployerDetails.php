<?php

namespace percipiolondon\staff\gql\types;

use GraphQL\Type\Definition\Type;
use craft\base\gql\GqlTypeTrait;

/**
 * Class OverseasEmployerDetails
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class OverseasEmployerDetails
{
    use GqlTypeTrait;

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'overseasEmployerDetails';
    }

    /**
     * List of fields for this type.
     *
     * @return array
     */
    public static function getFieldDefinitions(): array
    {
        return [
            'overseasEmployer' => [
                'name' => 'overseasEmployer',
                'type' => Type::boolean(),
            ],
            // TODO create enum
            'overseasSecondmentStatus' => [
                'name' => 'overseasSecondmentStatus',
                'type' => Type::string(),
            ],
            'eeaCitizen' => [
                'name' => 'eeaCitizen',
                'type' => Type::boolean(),
            ],
            'epm6Scheme' => [
                'name' => 'epm6Scheme',
                'type' => Type::boolean(),
            ],
        ];
    }
}
