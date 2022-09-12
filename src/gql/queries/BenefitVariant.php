<?php

namespace percipiolondon\staff\gql\queries;

use craft\gql\base\Query;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use percipiolondon\staff\gql\arguments\elements\BenefitVariant as Arguments;
use percipiolondon\staff\gql\arguments\elements\BenefitVariantEligibleEmployees;
use percipiolondon\staff\gql\resolvers\elements\BenefitVariantEligibleEmployees as BenefitVariantEligibleEmployeesResolver;
use percipiolondon\staff\gql\interfaces\elements\BenefitVariant as ElementInterface;
use percipiolondon\staff\gql\resolvers\elements\BenefitVariant as Resolver;
use percipiolondon\staff\gql\types\PersonalDetails;
use percipiolondon\staff\helpers\Gql as GqlHelper;

class BenefitVariant extends Query
{
    public static function getQueries($checkToken = true): array
    {
        if($checkToken && !GqlHelper::canQueryGroupBenefits()) {
            return [];
        }

        $queryType = new ObjectType([
            'name' => 'BenefitVariantEligibleEmployees',
            'fields' => [
                'id' => [
                    'type' => Type::string()
                ],
                'personalDetails' => [
                    'type' => PersonalDetails::getType(),
                ]
            ]
        ]);

        return [
            'BenefitVariants' => [
                'type' => Type::listOf(ElementInterface::getType()),
                'args' => Arguments::getArguments(),
                'resolve' => Resolver::class . '::resolve',
                'description' => 'This query is used to query for all the Benefit Variants',
                'complexity' => GqlHelper::relatedArgumentComplexity()
            ],
            'BenefitVariant' => [
                'type' => ElementInterface::getType(),
                'args' => Arguments::getArguments(),
                'resolve' => Resolver::class . '::resolveOne',
                'description' => 'This query is used to query one Benefit Variant',
                'complexity' => GqlHelper::relatedArgumentComplexity()
            ],
            'BenefitVariantEligibleEmployees' => [
                'type' => Type::listOf(ObjectType::getNamedType($queryType)),
                'args' => BenefitVariantEligibleEmployees::getArguments(),
                'resolve' => BenefitVariantEligibleEmployeesResolver::class . '::resolve',
                'complexity' => GqlHelper::relatedArgumentComplexity()
            ],
            'BenefitVariantCount' => [
                'type' => Type::nonNull(Type::int()),
                'args' => Arguments::getArguments(),
                'resolve' => Resolver::class . '::resolveCount',
                'description' => 'This query is used to return the number of Benefit Variants.',
                'complexity' => GqlHelper::singleQueryComplexity(),
            ],
        ];
    }
}