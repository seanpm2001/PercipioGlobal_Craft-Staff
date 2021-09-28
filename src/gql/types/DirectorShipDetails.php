<?php

namespace percipiolondon\craftstaff\gql\types;

use craft\gql\types\DateTime;

use GraphQL\Type\Definition\Type;

use percipiolondon\craftstaff\gql\base\GqlTypeTrait;

/**
 * Class DirectorShipDetails
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class DirectorShipDetails
{
    use GqlTypeTrait;

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'directorShipDetails';
    }

    /**
     * List of fields for this type.
     *
     * @return array
     */
    public static function getFieldDefinitions(): array
    {
        return [
            'isDirector' => [
                'name' => 'isDirector',
                'type' => Type::boolean(),
            ],
            'startDate' => [
                'name' => 'startDate',
                'type' => DateTime::getType(),
                'description' => 'Start date of directorship.',
            ],
            'leaveDate' => [
                'name' => 'leaveDate',
                'type' => DateTime::getType(),
                'description' => 'Leave date of directorship.',
            ],
            'niAlternativeMethod' => [
                'name' => 'niAlternativeMethod',
                'type' => Type::boolean(),
            ],
        ];
    }

}