<?php

namespace percipiolondon\craftstaff\gql\interfaces\elements;

use craft\gql\GqlEntityRegistry;
use craft\gql\interfaces\Element;
use craft\gql\TypeManager;
use craft\gql\types\DateTime;

use craft\helpers\Gql;
use craft\helpers\Json;

use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;

use percipiolondon\craftstaff\gql\types\generators\PayRunEntryType;
use percipiolondon\craftstaff\gql\types\NationalInsuranceCalculation;
use percipiolondon\craftstaff\gql\types\PayOptions;
use percipiolondon\craftstaff\gql\types\PayRunTotals;
use percipiolondon\craftstaff\gql\types\PensionSummary;
use percipiolondon\craftstaff\gql\types\UmbrellaPayment;
use percipiolondon\craftstaff\gql\types\ValueOverride;


class PayRunEntry extends Element
{
    /**
     * @inheritdoc
     */
    public static function getTypeGenerator(): string
    {
        return PayRunEntryType::class;
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
            'resolveType' => self::class . '::resolveElementTypeName',
        ]));

        PayRunEntryType::generateTypes();

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

        return TypeManager::prepareFieldDefinitions(array_merge(parent::getFieldDefinitions(), self::getConditionalFields(), [
            'staffologyId' => [
                'name' => 'staffologyId',
                'type' => Type::string(),
                'description' => 'The payrun id from staffology, needed for API calls.'
            ],
            'employerId' => [
                'name' => 'employerId',
                'type' => Type::int(),
                'description' => 'The id of the employer this payrun is for.',
            ],
            // TODO CREATE ENUM ??
            'taxYear' => [
                'name' => 'taxYear',
                'type' => Type::string(),
            ],
            'taxMonth' => [
                'name' => 'taxMonth',
                'type' => Type::int(),
                'description' => 'The Tax Month that the Payment Date falls in.',
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
            'note' => [
                'name' => 'note',
                'type' => Type::string(),
            ],
            'bacsSubReference' => [
                'name' => 'bacsSubReference',
                'type' => Type::string(),
                'description' => 'A randomly generated string for use with the RTI Hash Cross Reference',
            ],
            'bacsHashCode' => [
                'name' => 'bacsHashCode',
                'type' => Type::string(),
                'description' => 'A Hash Code used for RTI BACS Hash Cross Reference'
            ],
            'percentageOfWorkingDaysPaidAsNormal' => [
                'name' => 'percentageOfWorkingDaysPaidAsNormal',
                'type' => Type::float(),
            ],
            'workingDaysNotPaidAsNormal' => [
                'name' => 'workingDaysNotPaidAsNormal',
                'type' => Type::float(),
            ],
            // TODO CREATE ENUM
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
            ],
            'priorPayrollCode' => [
                'name' => 'priorPayrollCode',
                'type' => Type::string(),
            ],
            'payOptions' => [
                'name' => 'payOptions',
                'type' => PayOptions::getType(),
            ],
            'pensionSummary' => [
                'name' => 'pensionSummary',
                'type' => PensionSummary::getType(),
                'description' => 'If a PayRunEntry contains pension contributions then it\'ll also include a PensionSummary model giving further information about the Pension Scheme and the contributions made.'
            ],
            'totals' => [
                'name' => 'totals',
                'type' => PayRunTotals::getType(),
                'description' => 'Used to represent totals for a PayRun or PayRunEntry. If a value is 0 then it will not be shown.'
            ],
            'periodOverrides' => [
                'name' => 'periodOverrides',
                'type' => ValueOverride::getType(),
                'description' => 'Any calculated values for this period that should be overridden with a different value.'
            ],
            'totalYtd' => [
                'name' => 'totalYtd',
                'type' => PayRunTotals::getType(),
                'description' => 'Used to represent totals for a PayRun or PayRunEntry.'
            ],
            'totalsYtdOverrides' => [
                'name' => 'totalsYtdOverrides',
                'type' => ValueOverride::getType(),
                'description' => 'Any values of TotalsYtd that should be overridden with a different value.'
            ],
            'forcedCisVatAmount' => [
                'name' => 'forcedCisVatAmount',
                'type' => Type::float(),
            ],
            'holidayAccrued' => [
                'name' => 'holidayAccrued',
                'type' => Type::float(),
                'description' => 'The amount of holiday days accrued in the period.'
            ],
            // TODO CREATE ENUM
            'state' => [
                'name' => 'state',
                'type' => Type::string(),
                'description' => 'The state of the payrun. You would set this value when updating a payrun to finalise or re-open it.'
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
            'nationalInsuranceCalculation' => [
                'name' => 'nationalInsuranceCalculation',
                'type' => NationalInsuranceCalculation::getType(),
                'description' => 'Included as part of the PayRunEntry model to provide details of how the National Insurance Contribution was calculated.'
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
            'fps' => [
                'name' => 'fps',
                'type' => Type::boolean(),
            ],
            'emailId' => [
                'name' => 'emailId',
                'type' => Type::boolean(),
                'description' => 'If the Payslip for this PayRunEntry has been emailed to the employee then the Id for an EmployerEmail will be provided here.',
            ],
            'receivingOffsetPay' => [
                'name' => 'receivingOffsetPay',
                'type' => Type::boolean(),
                'description' => 'If the pay is being topped up due to an applied Leave having the offset value set to true then this will be set to true.',
            ],
            'paymentAfterLeaving' => [
                'name' => 'paymentAfterLeaving',
                'type' => Type::boolean(),
                'description' => 'If this payment is for an employee that has left then this is set to true.',
            ],
            'umbrellaPayment' => [
                'name' => 'umbrellaPayment',
                'type' => UmbrellaPayment::getType(),
            ],
            'id' => [
                'name' => 'id',
                'type' => Type::string(),
                'description' => 'The unique id of the object.',
            ],
        ]), self::getName());
    }

    /**
     * @inheritdoc
     */
    protected static function getConditionalFields(): array
    {
        return [];
    }
}