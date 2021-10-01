<?php

namespace percipiolondon\craftstaff\gql\types;

use craft\gql\types\DateTime;

use GraphQL\Type\Definition\Type;

use percipiolondon\craftstaff\gql\base\GqlTypeTrait;
use percipiolondon\craftstaff\gql\types\TieredPensionRate;
use percipiolondon\craftstaff\gql\types\WorkerGroup;


/**
 * Class PensionSummary
 *
 * @author Percipio Global Ltd. <support@percipio.london>
 * @since 1.0.0
 */
class PensionSummary
{
    use GqlTypeTrait;

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'pensionSummary';
    }

    /**
     * List of fields for this type.
     *
     * @return array
     */
    public static function getFieldDefinitions(): array
    {
        return [
            'pensionId' => [
                'name' => 'pensionId',
                'type' => Type::string(),
                'description' => 'The Id of the Pension.',
            ],
            'name' => [
                'name' => 'name',
                'type' => Type::string(),
                'description' => 'The name of the PensionScheme to which contributions have been made.',
            ],
            'pensionSchemeId' => [
                'name' => 'pensionSchemeId',
                'type' => Type::string(),
                'description' => 'The Id of the PensionScheme.',
            ],
            'startDate' => [
                'name' => 'startDate',
                'type' => DateTime::getType(),
                'description' => 'The start date of the period this PayRun covers.',
            ],
            'workerGroupId' => [
                'name' => 'workerGroupId',
                'type' => Type::string(),
                'description' => 'The Id of the WorkerGroup.',
            ],
            // TODO CREATE ENUM
            'pensionRule' => [
                'name' => 'pensionRule',
                'type' => Type::string(),
                'description' => 'The Id of the PensionScheme.',
            ],
            'papdisPensionProviderId' => [
                'name' => 'papdisPensionProviderId',
                'type' => Type::string(),
                'description' => 'Papdis information from the PensionScheme.',
            ],
            'papdisEmployerId' => [
                'name' => 'papdisEmployerId',
                'type' => Type::string(),
                'description' => 'Papdis information from the PensionScheme.',
            ],
            'employeePensionContributionMultiplier' => [
                'name' => 'employeePensionContributionMultiplier',
                'type' => Type::float(),
            ],
            'additionalVoluntaryContribution' => [
                'name' => 'additionalVoluntaryContribution',
                'type' => Type::float(),
            ],
            'avcIsPercentage' => [
                'name' => 'avcIsPercentage',
                'type' => Type::boolean(),
            ],
            'autoEnrolled' => [
                'name' => 'autoEnrolled',
                'type' => Type::boolean(),
            ],
            'workerGroup' => [
                'name' => 'workerGroup',
                'type' => WorkerGroup::getType(),
            ],
            'forcedTier' => [
                'name' => 'forcedTier',
                'type' => Type::string(),
                'description' => 'Papdis information from the PensionScheme.',
            ],
            'tiers' => [
                'name' => 'tiers',
                'type' => Type::listOf(TieredPensionRate::getType()),
                'description' => 'Papdis information from the PensionScheme.',
            ],
            'pensionablePayCodes' => [
                'name' => 'pensionablePayCodes',
                'type' => Type::listOf(Type::string()),
                'description' => 'Papdis information from the PensionScheme.',
            ],
        ];
    }

}
