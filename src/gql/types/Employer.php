<?php

namespace percipiolondon\staff\gql\types;

use craft\gql\base\GqlTypeTrait;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use percipiolondon\staff\gql\interfaces\elements\PayRun;
use percipiolondon\staff\helpers\Security as SecurityHelper;

/**
 * Class Employee
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class Employer
{
    use GqlTypeTrait;

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'employer';
    }

    /**
     * List of fields for this type.
     *
     * @return array
     */
    public static function getFieldDefinitions(): array
    {
        return [
            'crn' => [
                'name' => 'crn',
                'type' => Type::id(),
                'description' => 'The company registration number.',
                'resolve' => function($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return SecurityHelper::resolve($source, $resolveInfo);
                },
            ],
            'name' => [
                'name' => 'name',
                'type' => Type::string(),
                'description' => 'The company name.',
            ],
            'slug' => [
                'name' => 'slug',
                'type' => Type::nonNull(Type::string()),
                'description' => 'The company slug.',
            ],
            'currentYear' => [
                'name' => 'currentYear',
                'type' => Type::string(),
            ],
            'employeeCount' => [
                'name' => 'employeeCount',
                'type' => Type::int(),
            ],
            'staffologyId' => [
                'name' => 'staffologyId',
                'type' => Type::nonNull(Type::id()),
                'description' => 'The employer id from staffology, needed for API calls.',
            ],
            'startYear' => [
                'name' => 'startYear',
                'type' => Type::string(),
            ],
            'logoUrl' => [
                'name' => 'logoUrl',
                'type' => Type::string(),
            ],
            'currentPayRun' => [
                'name' => 'currentPayRun',
                'type' => PayRun::getType(),
                'description' => 'Current open pay run'
            ],
            'address' => [
                'name' => 'address',
                'type' => Address::getType(),
                'description' => 'The address.',
            ],
            'defaultPayOptions' => [
                'name' => 'defaultPayOptions',
                'type' => PayOptions::getType(),
                'description' => 'The companies default pay options'
            ],
            'hmrcDetails' => [
                'name' => 'hmrcDetails',
                'type' => HmrcDetails::getType(),
                'description' => 'The companies hmrc details'
            ]
        ];
    }
}
