<?php

namespace percipiolondon\staff\gql\types;

use percipiolondon\staff\gql\base\GqlTypeTrait;
use GraphQL\Type\Definition\Type;


/**
 * Class PartnerDetails
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class PartnerDetails
{
    use GqlTypeTrait;

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'partnerDetails';
    }

    /**
     * List of fields for this type.
     *
     * @return array
     */
    public static function getFieldDefinitions(): array
    {
        return [
            'firstName' => [
                'name' => 'firstName',
                'type' => Type::string(),
                'description' => 'First name.',
            ],
            'initials' => [
                'name' => 'initials',
                'type' => Type::string(),
                'description' => 'Initials.',
            ],
            'lastName' => [
                'name' => 'lastName',
                'type' => Type::string(),
                'description' => 'Last name.',
            ],
            'niNumber' => [
                'name' => 'niNumber',
                'type' => Type::string(),
                'description' => 'National insurance number.',
            ],
        ];
    }

}
