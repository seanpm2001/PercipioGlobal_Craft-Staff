<?php

namespace percipiolondon\staff\gql\types;

use craft\gql\base\GqlTypeTrait;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use percipiolondon\staff\helpers\Security;

/**
 * Class WorkerGroup
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class WorkerGroup
{
    use GqlTypeTrait;

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'workerGroup';
    }

    /**
     * List of fields for this type.
     *
     * @return array
     */
    public static function getFieldDefinitions(): array
    {
        return [
            'name' => [
                'name' => 'name',
                'type' => Type::string(),
            ],
            // TODO CREATE ENUM
            'contributionLevelType' => [
                'name' => 'contributionLevelType',
                'type' => Type::string(),
            ],
            'employeeContribution' => [
                'name' => 'employeeContribution',
                'type' => Type::float(),
                'resolve' => function ($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return Security::resolve($source, $resolveInfo, 'string');
                },
            ],
            'employeeContributionIsPercentage' => [
                'name' => 'employeeContributionIsPercentage',
                'type' => Type::boolean(),
            ],
            'employerContribution' => [
                'name' => 'employerContribution',
                'type' => Type::float(),
                'resolve' => function ($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return Security::resolve($source, $resolveInfo, 'float');
                },
            ],
            'employerContributionIsPercentage' => [
                'name' => 'employerContributionIsPercentage',
                'type' => Type::boolean(),
            ],
            'employerContributionTopUpPercentage' => [
                'name' => 'employerContributionTopUpPercentage',
                'type' => Type::float(),
                'description' => 'Increase Employer Contribution by this percentage of the Employee Contribution',
                'resolve' => function ($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return Security::resolve($source, $resolveInfo, 'float');
                },
            ],
            'customThreshold' => [
                'name' => 'customThreshold',
                'type' => Type::boolean(),
            ],
            'lowerLimit' => [
                'name' => 'lowerLimit',
                'type' => Type::float(),
                'resolve' => function ($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return Security::resolve($source, $resolveInfo, 'float');
                },
            ],
            'upperLimit' => [
                'name' => 'upperLimit',
                'type' => Type::float(),
                'resolve' => function ($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return Security::resolve($source, $resolveInfo, 'float');
                },
            ],
            'papdisGroup' => [
                'name' => 'papdisGroup',
                'type' => Type::string(),
            ],
            'papdisSubGroup' => [
                'name' => 'papdisSubGroup',
                'type' => Type::string(),
            ],
            'localAuthorityNumber' => [
                'name' => 'localAuthorityNumber',
                'type' => Type::string(),
                'description' => 'Only applicable if ContributionLevelType is Tp2020',
            ],
            'schoolEmployerType' => [
                'name' => 'schoolEmployerType',
                'type' => Type::string(),
                'description' => 'Only applicable if ContributionLevelType is Tp2020',
            ],
            'workerGroupId' => [
                'name' => 'workerGroupId',
                'type' => Type::string(),
            ],
            'id' => [
                'name' => 'id',
                'type' => Type::string(),
                'description' => 'The unique id of the object.',
            ],
        ];
    }
}
