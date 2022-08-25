<?php
/**
 * staff-management plugin for Craft CMS 3.x
 *
 * Craft Staff Management provides an HR solution for payroll and benefits
 *
 * @link      http://percipio.london
 * @copyright Copyright (c) 2021 Percipio
 */

namespace percipiolondon\staff;

use Craft;
use craft\base\Element;
use craft\base\Plugin;
use craft\console\Application as ConsoleApplication;
use craft\elements\User;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterGqlMutationsEvent;
use craft\events\RegisterGqlQueriesEvent;
use craft\events\RegisterGqlSchemaComponentsEvent;
use craft\events\RegisterGqlTypesEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\events\RegisterUserPermissionsEvent;
use craft\events\UserEvent;
use craft\helpers\UrlHelper;
use craft\services\Elements;
use craft\services\Gql;
use craft\services\UserPermissions;
use craft\services\Users;
use craft\web\twig\variables\CraftVariable;
use craft\web\UrlManager;

use nystudio107\pluginvite\services\VitePluginService;

use percipiolondon\staff\assetbundles\staff\StaffAsset;
use percipiolondon\staff\elements\BenefitProvider;
use percipiolondon\staff\elements\BenefitType ;
use percipiolondon\staff\elements\Employee as EmployeeElement;
use percipiolondon\staff\elements\Employer as EmployerElement;
use percipiolondon\staff\elements\History;
use percipiolondon\staff\elements\History as HistoryElement;
use percipiolondon\staff\elements\PayRun as PayRunElement;
use percipiolondon\staff\elements\PayRunEntry as PayRunEntryElement;
use percipiolondon\staff\elements\Request as RequestElement;
use percipiolondon\staff\elements\SettingsEmployee;
use percipiolondon\staff\gql\interfaces\elements\BenefitProvider as BenefitProviderInterface;
use percipiolondon\staff\gql\interfaces\elements\Employer as EmployerInterface;
use percipiolondon\staff\gql\interfaces\elements\Employee as EmployeeInterface;
use percipiolondon\staff\gql\interfaces\elements\PayRun as PayRunInterface;
use percipiolondon\staff\gql\interfaces\elements\PayRunEntry as PayRunEntryInterface;
use percipiolondon\staff\gql\interfaces\elements\Request as RequestInterface;
use percipiolondon\staff\gql\mutations\RequestMutation;
use percipiolondon\staff\gql\mutations\SettingsEmployeeMutation;
use percipiolondon\staff\gql\queries\BenefitProvider as BenefitProviderQueries;
use percipiolondon\staff\gql\queries\Employee as EmployeeQueries;
use percipiolondon\staff\gql\queries\Employer as EmployerQueries;
use percipiolondon\staff\gql\queries\History as HistoryQueries;
use percipiolondon\staff\gql\queries\PayRun as PayRunQueries;
use percipiolondon\staff\gql\queries\PayRunEntry as PayRunEntryQueries;
use percipiolondon\staff\gql\queries\Request as RequestQueries;
use percipiolondon\staff\gql\queries\SettingsEmployee as SettingsEmployeeQueries;
use percipiolondon\staff\gql\queries\Settings as SettingsQueries;
use percipiolondon\staff\models\Settings;
use percipiolondon\staff\plugin\Services as StaffServices;
use percipiolondon\staff\services\Addresses;
use percipiolondon\staff\services\Employees;
use percipiolondon\staff\services\Employers;
use percipiolondon\staff\services\GroupBenefits;
use percipiolondon\staff\services\PayOptions;
use percipiolondon\staff\services\PayRunEntries;
use percipiolondon\staff\services\PayRuns;
use percipiolondon\staff\services\Pensions;
use percipiolondon\staff\services\Totals;
use percipiolondon\staff\variables\StaffVariable;

use yii\base\Event;
use yii\base\ModelEvent;

/**
 * Craft plugins are very much like little applications in and of themselves. We’ve made
 * it as simple as we can, but the training wheels are off. A little prior knowledge is
 * going to be required to write a plugin.
 *
 * For the purposes of the plugin docs, we’re going to assume that you know PHP and SQL,
 * as well as some semi-advanced concepts like object-oriented programming and PHP namespaces.
 *
 * https://docs.craftcms.com/v3/extend/
 *
 * @author    Percipio
 * @package   Staff
 * @since     1.0.0
 *
 * @property  Settings              $settings
 * @property  VitePluginService     $vite
 * @property  Addresses $addresses
 * @property  GroupBenefits $groupBenefits
 * @property  Employees $employees
 * @property  Employers $employers
 * @property  PayOptions $payOptions
 * @property  PayRunEntries $payRunEntries
 * @property  PayRuns $payRuns
 * @property  Pensions $pensions
 * @property  Totals $totals
 * @property  UserPermissions $userPermissions
 */
class Staff extends Plugin
{
    use StaffServices;

    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * Staff::$plugin
     *
     * @var Staff
     */
    public static $plugin;

    /**
     * @var Settings
     */
    public static Settings $settings;

    /**
     * @var View
     */
    public static $view;

    // Public Properties
    // =========================================================================

    /**
     * To execute your plugin’s migrations, you’ll need to increase its schema version.
     *
     * @var string
     */
    public $schemaVersion = '1.0.3';

    /**
     * Set to `true` if the plugin should have its own section (main nav item) in the control panel.
     *
     * @var bool
     */
    public $hasCpSection = true;

    /**
     * Set to `true` if the plugin should have a settings view in the control panel.
     *
     * @var bool
     */
    public $hasCpSettings = true;



    // Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function __construct($id, $parent = null, array $config = [])
    {
        $config['components'] = [
            'staff' => Staff::class,
            // Register the vite service
            'vite' => [
                'class' => VitePluginService::class,
                'assetClass' => StaffAsset::class,
                'useDevServer' => true,
                'devServerPublic' => 'http://localhost:3951',
                'serverPublic' => 'http://localhost:3950',
                'errorEntry' => 'src/js/staff.ts',
                'devServerInternal' => 'http://craft-staff-buildchain:3951',
                'checkDevServer' => true,
            ],
        ];

        parent::__construct($id, $parent, $config);
    }



    // Public Methods
    // =========================================================================

    /**
     * Set our $plugin static property to this class so that it can be accessed via
     * Staff::$plugin
     *
     * Called after the plugin class is instantiated; do any one-time initialization
     * here such as hooks and events.
     *
     * If you have a '/vendor/autoload.php' file, it will be loaded for you automatically;
     * you do not need to load it in your init() method.
     *
     */
    public function init(): void
    {
        parent::init();
        self::$plugin = $this;

        // Initialize properties
        self::$settings = self::$plugin->getSettings();
        self::$view = Craft::$app->getView();

        $this->name = self::$settings->pluginName;

        $this->_setPluginComponents();

        $this->_registerGqlInterfaces();
        $this->_registerGqlSchemaComponents();

        $this->_registerGqlQueries();
        $this->_registerGqlMutations();
        $this->_registerElementTypes();
        $this->_registerControllers();

        $this->installEventListeners();

        /**
         * Logging in Craft involves using one of the following methods:
         *
         * Craft::trace(): record a message to trace how a piece of code runs. This is mainly for development use.
         * Craft::info(): record a message that conveys some useful information.
         * Craft::warning(): record a warning message that indicates something unexpected has happened.
         * Craft::error(): record a fatal error that should be investigated as soon as possible.
         *
         * Unless `devMode` is on, only Craft::warning() & Craft::error() will log to `craft/storage/logs/web.log`
         *
         * It's recommended that you pass in the magic constant `__METHOD__` as the second parameter, which sets
         * the category to the method (prefixed with the fully qualified class name) where the constant appears.
         *
         * To enable the Yii debug toolbar, go to your user account in the AdminCP and check the
         * [] Show the debug toolbar on the front end & [] Show the debug toolbar on the Control Panel
         *
         * http://www.yiiframework.com/doc-2.0/guide-runtime-logging.html
         */
        Craft::info(
            Craft::t(
                'staff-management',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    /**
     * @inheritdoc
     */
    public function getSettings()
    {
        return parent::getSettings();
    }

    /**
     * @inheritdoc
     */
    public function getSettingsResponse(): void
    {
        // redirect to plugin settings page
        Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('staff-management/plugin'));
    }

    /**
     * @inheritdoc
     */
    public function getCpNavItem(): array
    {
        $subNavs = [];
        $navItem = parent::getCpNavItem();
        /** @var User $currentUser */
        $request = Craft::$app->getRequest();
        $currentUser = Craft::$app->getUser()->getIdentity();
        // Only show sub navigation the user has permissions to view
        if ($currentUser->can('hub:dashboard')) {
            $subNavs['dashboard'] = [
                'label' => Craft::t('staff-management', 'Dashboard'),
                'url' => 'staff-management/dashboard',
            ];
        }
        if ($currentUser->can('hub:benefits')) {
            $subNavs['benefits'] = [
                'label' => Craft::t('staff-management', 'Group Benefits'),
                'url' => 'staff-management/benefits/providers',
            ];
        }
        if ($currentUser->can('hub:pay-runs')) {
            $subNavs['payRuns'] = [
                'label' => Craft::t('staff-management', 'Pay Runs'),
                'url' => 'staff-management/pay-runs',
            ];
        }
        if ($currentUser->can('hub:requests')) {
            $subNavs['requests'] = [
                'label' => Craft::t('staff-management', 'Requests'),
                'url' => 'staff-management/requests',
            ];
        }

        $editableSettings = true;
        // Check against allowAdminChanges
        if (!Craft::$app->getConfig()->getGeneral()->allowAdminChanges) {
            $editableSettings = false;
        }

        if ($currentUser->can('hub:plugin-settings') && $editableSettings) {
            $subNavs['plugin'] = [
                'label' => Craft::t('staff-management', 'Plugin settings'),
                'url' => 'staff-management/plugin',
            ];
        }

        return array_merge($navItem, [
            'subnav' => $subNavs,
        ]);
    }

    // Protected Methods
    // =========================================================================

    /**
     * Install our event listeners.
     */
    protected function installEventListeners(): void
    {
        $request = Craft::$app->getRequest();
        $this->installGlobalEventListeners();
        // Install our event listeners
        if ($request->getIsCpRequest() && !$request->getIsConsoleRequest()) {
            $this->installCpEventListeners();
        }

        self::$plugin->history->catchEventListeners();
    }

    /**
     * Install site event listeners for Control Panel requests only
     */
    protected function installCpEventListeners(): void
    {

        // Handler: UrlManager::EVENT_REGISTER_CP_URL_RULES
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function(RegisterUrlRulesEvent $event) {
                Craft::debug(
                    'UrlManager::EVENT_REGISTER_CP_URL_RULES',
                    __METHOD__
                );
                // Register our Control Panel routes
                $event->rules = array_merge(
                    $event->rules,
                    $this->customAdminCpRoutes()
                );
            }
        );

        // Handler: UserPermissions::EVENT_REGISTER_PERMISSIONS
        Event::on(
            UserPermissions::class,
            UserPermissions::EVENT_REGISTER_PERMISSIONS,
            function(RegisterUserPermissionsEvent $event) {
                Craft::debug(
                    'UserPermissions::EVENT_REGISTER_PERMISSIONS',
                    __METHOD__
                );
                // Register our custom permissions
                $event->permissions[Craft::t('staff-management', 'Staff')] = $this->customAdminCpPermissions();
            }
        );
    }

    /**
     * Creates and returns the model used to store the plugin’s settings.
     *
     * @return \craft\base\Model|null
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * Return the custom Control Panel routes
     *
     * @return array
     */
    protected function customAdminCpRoutes(): array
    {
        return [
            'staff-management' => 'staff-management/settings/dashboard',
            'staff-management/dashboard' => 'staff-management/settings/dashboard',
            'staff-management/benefits/providers' => 'staff-management/benefit-provider',
            'staff-management/benefits/providers/new' => 'staff-management/benefit-provider/edit',
            'staff-management/benefits/providers/edit/<providerId:\d+>' => 'staff-management/benefit-provider/edit',
            'staff-management/benefits/providers/<providerId:\d+>' => 'staff-management/benefit-provider/detail',
            'staff-management/benefits/types' => 'staff-management/benefit-type',
            'staff-management/benefits/types/new' => 'staff-management/benefit-type/edit',
            'staff-management/benefits/types/edit/<typeId:\d+>/<benefitType:\S+>' => 'staff-management/benefit-type/edit',
            'staff-management/benefits/types/<typeId:\d+>' => 'staff-management/benefit-type/detail',
            'staff-management/pay-runs' => 'staff-management/pay-run',
            'staff-management/pay-runs/queue' => 'staff-management/pay-run/get-queue',
            'staff-management/pay-runs/<employerId:\d+>/<currentYear:\w+>' => 'staff-management/pay-run/pay-run-by-employer',
            'staff-management/pay-runs/<employerId:\d+>/<currentYear:\w+>/<payRunId:\d+>' => 'staff-management/pay-run/detail',
            'staff-management/pay-runs/fetch-pay-runs/<employerId:\d+>' => 'staff-management/pay-run/fetch-pay-runs',
            'staff-management/pay-runs/fetch-pay-runs/<employerId:\d+>/<taxYear:\w+>' => 'staff-management/pay-run/fetch-pay-runs',
            'staff-management/pay-runs/fetch-pay-run/<payRunId:\d+>' => 'staff-management/pay-run/fetch-pay-run',
            'staff-management/pay-runs/get-logs/<payRunId:\d+>' => 'staff-management/pay-run/get-pay-run-logs',
            'staff-management/pay-runs/download-template/<payRunId:\d+>' => 'staff-management/pay-run/download-template',
            'staff-management/requests' => 'staff-management/request',
            'staff-management/requests/<requestId:\d+>' => 'staff-management/request/detail',
            'staff-management/requests/undo/<requestId:\d+>' => 'staff-management/request/undo',
            'staff-management/plugin' => 'staff-management/settings/plugin',
            'staff-management/settings/get-gql-token' => 'staff-management/settings/get-gql-token',
        ];
    }

    /**
     * Return the custom Control Panel user permissions.
     *
     * @return array
     */
    protected function customAdminCpPermissions(): array
    {
        return [
            'hub:dashboard' => [
                'label' => Craft::t('staff-management', 'Dashboard'),
            ],
            'hub:benefits' => [
                'label' => Craft::t('staff-management', 'Benefit Provider'),
            ],
            'hub:group-benefits' => [
                'label' => Craft::t('staff-management', 'Group Benefit Provider'),
            ],
            'hub:pay-runs' => [
                'label' => Craft::t('staff-management', 'Pay Runs'),
            ],
            'hub:requests' => [
                'label' => Craft::t('staff-management', 'Requests'),
            ],
            'hub:plugin-settings' => [
                'label' => Craft::t('staff-management', 'Edit Plugin Settings'),
            ],
        ];
    }

    /**
     * Install global event listeners for all request types
     */
    protected function installGlobalEventListeners(): void
    {
        // Handler: CraftVariable::EVENT_INIT
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function(Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('staff', [
                   'class' => StaffVariable::class,
                   'viteService' => $this->vite,
               ]);
            }
        );
    }


    // Public Methods
    // =========================================================================




    // Private Methods
    // =========================================================================

    private function _registerGqlInterfaces(): void
    {
        Event::on(
            Gql::class,
            Gql::EVENT_REGISTER_GQL_TYPES,
            function(RegisterGqlTypesEvent $event) {
                $event->types[] = BenefitProviderInterface::class;
                $event->types[] = EmployerInterface::class;
                $event->types[] = EmployeeInterface::class;
                $event->types[] = PayRunInterface::class;
                $event->types[] = PayRunEntryInterface::class;
                $event->types[] = RequestInterface::class;
            }
        );
    }

    private function _registerGqlSchemaComponents(): void
    {
        Event::on(
            Gql::class,
            Gql::EVENT_REGISTER_GQL_SCHEMA_COMPONENTS,
            function(RegisterGqlSchemaComponentsEvent $event) {
                $event->queries = array_merge($event->queries, [
                    'Benefits' => [
                        // benefits component with read action, labelled “View Benefit Providers” in UI
                        'benefitproviders:read' => ['label' => Craft::t('staff-management', 'View Benefit Providers')],
                    ],
                    'Staff Management' => [
                        // employers component with read action, labelled “View Employers” in UI
                        'employers:read' => ['label' => Craft::t('staff-management', 'View Employers')],
                        // employees component with read action, labelled “View Employees” in UI
                        'employees:read' => ['label' => Craft::t('staff-management', 'View Employees')],
                        // request component with read action, labelled "View Request" in UI
                        'requests:read' => ['label' => Craft::t('staff-management', 'View Requests')],
                    ],
                    'PayRunEntries' => [
                        // payruns entries component with read action, labelled “View Payruns” in UI
                        'payrunentries:read' => ['label' => Craft::t('staff-management', 'View Payrun Entries')],
                        // payruns component with read action, labelled “View Payruns” in UI
                        'payruns:read' => ['label' => Craft::t('staff-management', 'View Payruns')],
                    ],
                ]);

                $event->mutations = array_merge($event->mutations, [
                    'Staff Management' => [
                        // request component with create action, labelled "Create Requests" in UI
                        'requests:create' => ['label' => Craft::t('staff-management', 'Edit Requests')],
                    ]
                ]);
            }
        );
    }

    private function _registerGqlQueries(): void
    {
        Event::on(
            Gql::class,
            Gql::EVENT_REGISTER_GQL_QUERIES,
            function(RegisterGqlQueriesEvent $event) {
                $event->queries = array_merge(
                    $event->queries,
                    BenefitProviderQueries::getQueries(),
                    EmployerQueries::getQueries(),
                    EmployeeQueries::getQueries(),
                    HistoryQueries::getQueries(),
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
            Gql::class,
            Gql::EVENT_REGISTER_GQL_MUTATIONS,
            function(RegisterGqlMutationsEvent $event) {
                $event->mutations = array_merge(
                    $event->mutations,
                    RequestMutation::getMutations(),
                    SettingsEmployeeMutation::getMutations(),
                );
            }
        );
    }

    private function _registerElementTypes(): void
    {
        // Register our elements
        Event::on(
            Elements::class,
            Elements::EVENT_REGISTER_ELEMENT_TYPES,
            function(RegisterComponentTypesEvent $event) {
                $event->types[] = BenefitProvider::class;
                $event->types[] = BenefitType::class;
                $event->types[] = EmployerElement::class;
                $event->types[] = EmployeeElement::class;
                $event->types[] = HistoryElement::class;
                $event->types[] = PayRunElement::class;
                $event->types[] = PayRunEntryElement::class;
                $event->types[] = RequestElement::class;
                $event->types[] = SettingsEmployee::class;
            }
        );
    }

    private function _registerControllers(): void
    {
        // Add in our console commands
        if (Craft::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'percipiolondon\staff\console\controllers';
        }
    }
}
