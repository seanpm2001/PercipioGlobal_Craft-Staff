<?php

namespace percipiolondon\craftstaff\gql\types;

use craft\gql\types\DateTime;

use percipiolondon\craftstaff\gql\base\GqlTypeTrait;
use percipiolondon\craftstaff\gql\types\CisVerificationDetails;
use GraphQL\Type\Definition\Type;


/**
 * Class CisDetails
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class CisDetails
{
    use GqlTypeTrait;

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'cisDetails';
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
            'utr' => [
                'name' => 'utr',
                'type' => Type::string(),
            ],
            'tradingName' => [
                'name' => 'tradingName',
                'type' => Type::string(),
            ],
            'companyUtr' => [
                'name' => 'companyUtr',
                'type' => Type::string(),
            ],
            'companyNumber' => [
                'name' => 'companyNumber',
                'type' => Type::string(),
            ],
            'vatRegistered' => [
                'name' => 'vatRegistered',
                'type' => Type::boolean(),
            ],
            'vatNumber' => [
                'name' => 'vatNumber',
                'type' => Type::string(),
            ],
            'vatRate' => [
                'name' => 'vatRate',
                'type' => Type::float(),
            ],
            'verification' => [
                'name' => 'verification',
                'type' => CisVerificationDetails::getType(),
            ],
        ];
    }

}
