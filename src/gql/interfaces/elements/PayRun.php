<?php

namespace percipiolondon\staff\gql\interfaces\elements;

use craft\gql\interfaces\Element;
use craft\gql\GqlEntityRegistry;
use craft\gql\TypeLoader;
use craft\gql\TypeManager;
use craft\gql\types\DateTime;
use craft\helpers\Gql;
use craft\helpers\Json;

use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;

use percipiolondon\staff\elements\PayRun as PayRunElement;
use percipiolondon\staff\gql\types\PayRunTotals;
use percipiolondon\staff\gql\types\generators\PayRunGenerator;
use percipiolondon\staff\helpers\Security as SecurityHelper;


class PayRun extends Element
{
    /**
     * @inheritdoc
     */
    public static function getTypeGenerator(): string
    {
        return PayRunGenerator::class;
    }

    /**
     * @inheritdoc
     */
    public static function getType($fields = null): Type
    {

        if ($type = GqlEntityRegistry::getEntity(self::getName())) {
            return $type;
        }

        $type = GqlEntityRegistry::createEntity(self::getName(), new InterfaceType([
            'name' => static::getName(),
            'fields' => self::class . '::getFieldDefinitions',
            'description' => 'This is the interface implemented for all payruns.',
            'resolveType' => function(PayRunElement $value) {
                return $value->getGqlTypeName();
            }
        ]));

        PayRunGenerator::generateTypes();

        return $type;
    }

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'PayRunInterface';
    }

    /**
     * @inheritdoc
     */
    public static function getFieldDefinitions(): array
    {
        $parentFields = parent::getFieldDefinitions();

        $fields = [
            'staffologyId' => [
                'name' => 'staffologyId',
                'type' => Type::string(),
                'description' => 'The payruns id from staffology, needed for API calls.'
            ],
            'employerId' => [
                'name' => 'employerId',
                'type' => Type::int(),
                'description' => 'The id of the employer this payruns is for.',
            ],
            'taxYear' => [
                'name' => 'taxYear',
                'type' => Type::string(),
            ],
            'taxMonth' => [
                'name' => 'taxMonth',
                'type' => Type::int(),
                'description' => 'The Tax Month that the Payment Date falls in.',
            ],
            'payPeriod' => [
                'name' => 'payPeriod',
                'type' => Type::string(),
            ],
            'ordinal' => [
                'name' => 'ordinal',
                'type' => Type::int(),
                'description' => 'Indicates whether this uses first, second, third (etc.) PaySchedule for this PayPeriod.',
            ],
            'period' => [
                'name' => 'period',
                'type' => Type::int(),
                'description' => 'The period (i.e, Tax Week or Tax Month) that this PayRun is for.',
            ],
            'startDate' => [
                'name' => 'startDate',
                'type' => DateTime::getType(),
                'description' => 'The start date of the period this PayRun covers.',
            ],
            'endDate' => [
                'name' => 'endDate',
                'type' => DateTime::getType(),
                'description' => 'The end date of the period this PayRun covers.',
            ],
            'paymentDate' => [
                'name' => 'paymentDate',
                'type' => DateTime::getType(),
                'description' => 'The intended date that Employees will be paid, although this can be changed on a per PayRunEntry basis.',
            ],
            'employeeCount' => [
                'name' => 'employeeCount',
                'type' => Type::int(),
                'description' => 'The number of Employees included in this PayRun (including any CIS Subcontractors).',
            ],
            'subContractorCount' => [
                'name' => 'subContractorCount',
                'type' => Type::int(),
                'description' => 'The number of CIS Subcontractors included in this PayRun.',
            ],
            'state' => [
                'name' => 'state',
                'type' => Type::string(),
                'description' => 'The state of the payruns. You would set this value when updating a payruns to finalise or re-open it.',
            ],
            'isClosed' => [
                'name' => 'isClosed',
                'type' => Type::boolean(),
                'description' => 'If the PayRun is Finalised and changes can no longer be made.',
            ],
            'dateClosed' => [
                'name' => 'dateClosed',
                'type' => DateTime::getType(),
            ],
            'totals' => [
                'name' => 'totals',
                'type' => PayRunTotals::getType(),
                'description' => 'Totals of the payrun.',
            ]
        ];

        return TypeManager::prepareFieldDefinitions(array_merge($parentFields, $fields), self::getName());
    }
}