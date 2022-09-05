<?php

namespace percipiolondon\staff\gql\types;

use craft\gql\base\GqlTypeTrait;
use craft\gql\types\DateTime;
use craft\helpers\DateTimeHelper;
use craft\helpers\Gql;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;


/**
 * Class TieredPensionRate
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class Policy
{
    use GqlTypeTrait;

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'policy';
    }

    /**
     * List of fields for this type.
     *
     * @return array
     */
    public static function getFieldDefinitions(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::int(),
            ],
            'internalCode' => [
                'name' => 'internalCode',
                'type' => Type::string(),
            ],
            'providerId' => [
                'name' => 'providerId',
                'type' => Type::int(),
            ],
            'employerId' => [
                'name' => 'employerId',
                'type' => Type::int(),
            ],
            'benefitTypeId' => [
                'name' => 'benefitTypeId',
                'type' => Type::int(),
            ],
            'status' => [
                'name' => 'status',
                'type' => Type::string(),
            ],
            'policyName' => [
                'name' => 'policyName',
                'type' => Type::string(),
            ],
            'policyNumber' => [
                'name' => 'policyNumber',
                'type' => Type::string(),
            ],
            'policyHolder' => [
                'name' => 'policyHolder',
                'type' => Type::string(),
            ],
            'policyStartDate' => [
                'name' => 'policyStartDate',
                'type' => DateTime::getType(),
                'resolve' => function ($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return Gql::applyDirectives($source, $resolveInfo, DateTimeHelper::toDateTime($source->policyStartDate));
                }
            ],
            'policyRenewalDate' => [
                'name' => 'policyRenewalDate',
                'type' => DateTime::getType(),
                'resolve' => function ($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return Gql::applyDirectives($source, $resolveInfo, DateTimeHelper::toDateTime($source->policyRenewalDate));
                }
            ],
            'paymentFrequency' => [
                'name' => 'paymentFrequency',
                'type' => Type::string(),
            ],
            'commissionRate' => [
                'name' => 'commissionRate',
                'type' => Type::float(),
            ],
            'description' => [
                'name' => 'description',
                'type' => Type::string(),
            ],
        ];
    }
}
