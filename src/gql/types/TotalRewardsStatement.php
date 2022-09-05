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
class TotalRewardsStatement
{
    use GqlTypeTrait;

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'totalRewardStatement';
    }

    /**
     * List of fields for this type.
     *
     * @return array
     */
    public static function getFieldDefinitions(): array
    {
        return [
            'title' => [
                'name' => 'title',
                'type' => Type::string(),
            ],
            'monetaryValue' => [
                'name' => 'monetaryValue',
                'type' => Type::float(),
            ],
            'startDate' => [
                'name' => 'startDate',
                'type' => DateTime::getType(),
                'resolve' => function ($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return Gql::applyDirectives($source, $resolveInfo, DateTimeHelper::toDateTime($source['startDate']));
                }
            ],
            'endDate' => [
                'name' => 'endDate',
                'type' => DateTime::getType(),
                'resolve' => function ($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return Gql::applyDirectives($source, $resolveInfo, DateTimeHelper::toDateTime($source['endDate']));
                }
            ],
        ];
    }
}
