<?php

namespace percipiolondon\staff\gql\interfaces\elements;

use craft\gql\GqlEntityRegistry;
use craft\gql\interfaces\Element;
use craft\gql\TypeManager;
use craft\gql\types\DateTime;
use craft\helpers\DateTimeHelper;
use craft\helpers\Gql;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use percipiolondon\staff\elements\PayRunEntry as PayRunEntryElement;
use percipiolondon\staff\gql\types\Employee;
use percipiolondon\staff\gql\types\generators\PayRunEntryGenerator;
use percipiolondon\staff\gql\types\PayRunTotals;
use percipiolondon\staff\gql\types\PensionSummary;
use percipiolondon\staff\helpers\Security as SecurityHelper;

class PayRunEntry extends Element
{
    /**
     * @inheritdoc
     */
    public static function getTypeGenerator(): string
    {
        return PayRunEntryGenerator::class;
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
            'description' => 'This is the interface implemented for all payrun entries.',
            'resolveType' => function(PayRunEntryElement $value) {
                return $value->getGqlTypeName();
            },
        ]));

        PayRunEntryGenerator::generateTypes();

        return $type;
    }

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'PayRunEntryInterface';
    }

    /**
     * @inheritdoc
     */
    public static function getFieldDefinitions(): array
    {
        $parentFields = parent::getFieldDefinitions();

        $securedFields = [
            'pdf' => [
                'name' => 'pdf',
                'type' => Type::string(),
                'description' => 'The payslip pdf',
                'resolve' => function($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return SecurityHelper::resolve($source, $resolveInfo);
                },
            ],
        ];

        $fields = [
            'staffologyId' => [
                'name' => 'staffologyId',
                'type' => Type::string(),
                'description' => 'The payruns id from staffology, needed for API calls.',
            ],
            'employerId' => [
                'name' => 'employerId',
                'type' => Type::int(),
                'description' => 'The id of the employer this pay run entry is from.',
            ],
            'employeeId' => [
                'name' => 'employeeId',
                'type' => Type::int(),
                'description' => 'The id of the employee this pay run entry is for.',
            ],
            'taxYear' => [
                'name' => 'taxYear',
                'type' => Type::string(),
            ],
            'startDate' => [
                'name' => 'startDate',
                'type' => DateTime::getType(),
                'description' => 'The start date of the period this PayRun covers.',
                'resolve' => function ($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return Gql::applyDirectives($source, $resolveInfo, DateTimeHelper::toDateTime($source->startDate));
                }
            ],
            'endDate' => [
                'name' => 'endDate',
                'type' => DateTime::getType(),
                'description' => 'The end date of the period this PayRun covers.',
                'resolve' => function ($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return Gql::applyDirectives($source, $resolveInfo, DateTimeHelper::toDateTime($source->endDate));
                }
            ],
            'note' => [
                'name' => 'note',
                'type' => Type::string(),
            ],
            'bacsSubReference' => [
                'name' => 'bacsSubReference',
                'type' => Type::string(),
                'description' => 'A randomly generated string for use with the RTI Hash Cross Reference',
            ],
            'percentageOfWorkingDaysPaidAsNormal' => [
                'name' => 'percentageOfWorkingDaysPaidAsNormal',
                'type' => Type::float(),
            ],
            'workingDaysNotPaidAsNormal' => [
                'name' => 'workingDaysNotPaidAsNormal',
                'type' => Type::float(),
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
            'isNewStarter' => [
                'name' => 'isNewStarter',
                'type' => Type::boolean(),
                'description' => 'Determines whether or not this Employee will be declared as a new starter on the resulting FPS.',
            ],
            'unpaidAbsence' => [
                'name' => 'unpaidAbsence',
                'type' => Type::boolean(),
                'description' => 'Indicates that there was unpaid absence in the pay period.',
            ],
            'hasAttachmentOrders' => [
                'name' => 'hasAttachmentOrders',
                'type' => Type::boolean(),
                'description' => 'Indicates that there are AttachmentOrders for this Employee in this entry.',
            ],
            'paymentDate' => [
                'name' => 'paymentDate',
                'type' => DateTime::getType(),
                'description' => 'The intended date that Employees will be paid, although this can be changed on a per PayRunEntry basis.',
                'resolve' => function ($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                    return Gql::applyDirectives($source, $resolveInfo, DateTimeHelper::toDateTime($source->startDate));
                }
            ],
            'forcedCisVatAmount' => [
                'name' => 'forcedCisVatAmount',
                'type' => Type::float(),
            ],
            'holidayAccrued' => [
                'name' => 'holidayAccrued',
                'type' => Type::float(),
                'description' => 'The amount of holiday days accrued in the period.',
            ],
            'state' => [
                'name' => 'state',
                'type' => Type::string(),
                'description' => 'The state of the payruns. You would set this value when updating a payruns to finalise or re-open it.',
            ],
            'isClosed' => [
                'name' => 'isClosed',
                'type' => Type::boolean(),
            ],
            'manualNi' => [
                'name' => 'manualNi',
                'type' => Type::boolean(),
                'description' => 'If set to true then you must provide your own value for NationalInsuranceCalculation.',
            ],
            'payrollCodeChanged' => [
                'name' => 'payrollCodeChanged',
                'type' => Type::boolean(),
                'description' => 'Indicates whether or not the Payroll Code for this Employee has changed since the last FPS.',
            ],
            'aeNotEnroledWarning' => [
                'name' => 'aeNotEnroledWarning',
                'type' => Type::boolean(),
                'description' => 'If true then this Employee needs to be on an Auto Enrolment pension but isn\'t yet.',
            ],
            'receivingOffsetPay' => [
                'name' => 'receivingOffsetPay',
                'type' => Type::boolean(),
                'description' => 'If the pay is being topped up due to an applied Leave having the offset value set to true then this will be set to true.',
            ],
            'employer' => [
                'name' => 'employer',
                'type' => Type::string(),
                'description' => 'The company name of where the pay run belongs to'
            ],
            'employee' => [
                'name' => 'employee',
                'type' => Employee::getType(),
                'description' => 'The employee of where the pay run entry belongs to'
            ],
            'pensionSummary' => [
                'name' => 'pensionSummary',
                'type' => PensionSummary::getType(),
                'description' => 'The employee pension summary'
            ],
            'totals' => [
                'name' => 'totals',
                'type' => PayRunTotals::getType(),
                'description' => 'Totals of the payrun.',
            ],
        ];

        return TypeManager::prepareFieldDefinitions(array_merge($parentFields, $securedFields, $fields), self::getName());
    }
}
