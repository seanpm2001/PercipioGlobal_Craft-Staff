<?php

namespace percipiolondon\staff\plugin;

use Craft;
use craft\events\RegisterGqlMutationsEvent;
use craft\events\RegisterGqlQueriesEvent;
use craft\events\RegisterGqlSchemaComponentsEvent;
use craft\events\RegisterGqlTypesEvent;
use craft\gql\TypeLoader;
use craft\services\Gql as GqlService;
use percipiolondon\staff\gql\interfaces\elements\BenefitVariant as BenefitVariantInterface;
use percipiolondon\staff\gql\interfaces\elements\Employee as EmployeeInterface;
use percipiolondon\staff\gql\interfaces\elements\Employer as EmployerInterface;
use percipiolondon\staff\gql\interfaces\elements\History as HistoryInterface;
use percipiolondon\staff\gql\interfaces\elements\Notification as NotificationInterface;
use percipiolondon\staff\gql\interfaces\elements\PayRun as PayRunInterface;
use percipiolondon\staff\gql\interfaces\elements\PayRunEntry as PayRunEntryInterface;
use percipiolondon\staff\gql\interfaces\elements\Request as RequestInterface;
use percipiolondon\staff\gql\mutations\BenefitVariantEmployeesMutation;
use percipiolondon\staff\gql\mutations\NotificationMutation;
use percipiolondon\staff\gql\mutations\RequestMutation;
use percipiolondon\staff\gql\mutations\SettingsEmployeeMutation;
use percipiolondon\staff\gql\queries\BenefitVariant as BenefitVariantQueries;
use percipiolondon\staff\gql\queries\Employee as EmployeeQueries;
use percipiolondon\staff\gql\queries\Employer as EmployerQueries;
use percipiolondon\staff\gql\queries\History as HistoryQueries;
use percipiolondon\staff\gql\queries\Notifications as NotificationQueries;
use percipiolondon\staff\gql\queries\PayRun as PayRunQueries;
use percipiolondon\staff\gql\queries\PayRunEntry as PayRunEntryQueries;
use percipiolondon\staff\gql\queries\Request as RequestQueries;
use percipiolondon\staff\gql\queries\Settings as SettingsQueries;
use percipiolondon\staff\gql\queries\SettingsEmployee as SettingsEmployeeQueries;
use percipiolondon\staff\gql\types\Address;
use percipiolondon\staff\gql\types\BenefitProvider;
use percipiolondon\staff\gql\types\BenefitVariantGcic;
use percipiolondon\staff\gql\types\Employee;
use percipiolondon\staff\gql\types\Employer;
use percipiolondon\staff\gql\types\EmploymentDetails;
use percipiolondon\staff\gql\types\HmrcDetails;
use percipiolondon\staff\gql\types\PayOptions;
use percipiolondon\staff\gql\types\PayRunTotals;
use percipiolondon\staff\gql\types\PensionSummary;
use percipiolondon\staff\gql\types\PersonalDetails;
use percipiolondon\staff\gql\types\Policy;
use percipiolondon\staff\gql\types\StarterDetails;
use percipiolondon\staff\gql\types\TotalRewardsStatement;
use yii\base\Event;

trait Gql
{
    private function _registerGql()
    {
        $this->_registerGqlInterfaces();
        $this->_registerGqlTypes();
        $this->_registerGqlSchemaComponents();

        $this->_registerGqlQueries();
        $this->_registerGqlMutations();
    }


    private function _registerGqlInterfaces(): void
    {
        Event::on(
            \craft\services\Gql::class,
            GqlService::EVENT_REGISTER_GQL_TYPES,
            function(RegisterGqlTypesEvent $event) {
                $event->types[] = BenefitVariantInterface::class;
                $event->types[] = EmployerInterface::class;
                $event->types[] = EmployeeInterface::class;
                $event->types[] = HistoryInterface::class;
                $event->types[] = NotificationInterface::class;
                $event->types[] = PayRunInterface::class;
                $event->types[] = PayRunEntryInterface::class;
                $event->types[] = RequestInterface::class;
            }
        );
    }

    private function _registerGqlTypes(): void
    {
        TypeLoader::registerType(Address::getName(), Address::class . '::getType');
        TypeLoader::registerType(BenefitProvider::getName(), BenefitProvider::class . '::getType');
        TypeLoader::registerType(BenefitVariantGcic::getName(), BenefitVariantGcic::class . '::getType');
        TypeLoader::registerType(Employee::getName(), Employee::class . '::getType');
        TypeLoader::registerType(Employer::getName(), Employer::class . '::getType');
        TypeLoader::registerType(EmploymentDetails::getName(), EmploymentDetails::class . '::getType');
        TypeLoader::registerType(HmrcDetails::getName(), HmrcDetails::class . '::getType');
        TypeLoader::registerType(LeaveSettings::getName(), LeaveSettings::class . '::getType');
        TypeLoader::registerType(PayOptions::getName(), PayOptions::class . '::getType');
        TypeLoader::registerType(PayRunTotals::getName(), PayRunTotals::class . '::getType');
        TypeLoader::registerType(PensionSummary::getName(), PensionSummary::class . '::getType');
        TypeLoader::registerType(PersonalDetails::getName(), PersonalDetails::class . '::getType');
        TypeLoader::registerType(Policy::getName(), Policy::class . '::getType');
        TypeLoader::registerType(StarterDetails::getName(), StarterDetails::class . '::getType');
        TypeLoader::registerType(TotalRewardsStatement::getName(), TotalRewardsStatement::class . '::getType');
        TypeLoader::registerType(WorkerGroup::getName(), WorkerGroup::class . '::getType');

    }

    private function _registerGqlSchemaComponents(): void
    {
        Event::on(
            GqlService::class,
            GqlService::EVENT_REGISTER_GQL_SCHEMA_COMPONENTS,
            function(RegisterGqlSchemaComponentsEvent $event) {
                $event->queries = array_merge($event->queries, [
                    'Staff Management' => [
                        'group-benefits:read' => ['label' => Craft::t('staff-management', 'View Group Benefits')],
                        'employers:read' => ['label' => Craft::t('staff-management', 'View Employers')],
                        'employees:read' => ['label' => Craft::t('staff-management', 'View Employees')],
                        'history:read' => ['label' => Craft::t('staff-management', 'View History')],
                        'notifications:read' => ['label' => Craft::t('staff-management', 'View Notifications')],
                        'pay-run-entries:read' => ['label' => Craft::t('staff-management', 'View Pay Run Entries')],
                        'pay-runs:read' => ['label' => Craft::t('staff-management', 'View Pay Runs')],
                        'requests:read' => ['label' => Craft::t('staff-management', 'View Requests')],
                        'settings-employee:read' => ['label' => Craft::t('staff-management', 'View Employee Settings')],
                        'settings:read' => ['label' => Craft::t('staff-management', 'View Settings')],
                    ],
                ]);

                $event->mutations = array_merge($event->mutations, [
                    'Staff Management' => [
                        'benefit-employees:create' => ['label' => Craft::t('staff-management', 'Add Employees To A Benefit Variant')],
                        'notifications:update' => ['label' => Craft::t('staff-management', 'Update Notifications')],
                        'requests:create' => ['label' => Craft::t('staff-management', 'Edit Requests')],
                        'settings-employee:update' => ['label' => Craft::t('staff-management', 'Update Employee Settings')],
                    ]
                ]);
            }
        );
    }

    private function _registerGqlQueries(): void
    {
        Event::on(
            GqlService::class,
            GqlService::EVENT_REGISTER_GQL_QUERIES,
            function(RegisterGqlQueriesEvent $event) {
                $event->queries = array_merge(
                    $event->queries,
                    BenefitVariantQueries::getQueries(),
                    EmployerQueries::getQueries(),
                    EmployeeQueries::getQueries(),
                    HistoryQueries::getQueries(),
                    NotificationQueries::getQueries(),
                    PayRunQueries::getQueries(),
                    PayRunEntryQueries::getQueries(),
                    RequestQueries::getQueries(),
                    SettingsQueries::getQueries(),
                    SettingsEmployeeQueries::getQueries(),
                );
            }
        );
    }

    private function _registerGqlMutations()
    {
        Event::on(
            GqlService::class,
            GqlService::EVENT_REGISTER_GQL_MUTATIONS,
            function(RegisterGqlMutationsEvent $event) {
                $event->mutations = array_merge(
                    $event->mutations,
                    BenefitVariantEmployeesMutation::getMutations(),
                    NotificationMutation::getMutations(),
                    RequestMutation::getMutations(),
                    SettingsEmployeeMutation::getMutations(),
                );
            }
        );
    }
}