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
use craft\base\Plugin;
use craft\console\Application as ConsoleApplication;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterGqlQueriesEvent;
use craft\events\RegisterGqlSchemaComponentsEvent;
use craft\events\RegisterGqlTypesEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\events\RegisterUserPermissionsEvent;
use craft\helpers\UrlHelper;
use craft\services\Elements;
use craft\services\Gql;
use craft\services\Plugins;
use craft\services\UserPermissions;
use craft\web\UrlManager;
use craft\web\twig\variables\CraftVariable;

use nystudio107\pluginvite\services\VitePluginService;

use percipiolondon\staff\assetbundles\staff\StaffAsset;
use percipiolondon\staff\models\Settings;
use percipiolondon\staff\elements\Employer as EmployerElement;
use percipiolondon\staff\elements\Employee as EmployeeElement;
use percipiolondon\staff\elements\PayRun as PayRunElement;
use percipiolondon\staff\elements\PayRunEntry as PayRunEntryElement;
use percipiolondon\staff\elements\HardingUser as HardingUserElement;
use percipiolondon\staff\gql\queries\Employer as EmployerQueries;
use percipiolondon\staff\gql\queries\Employee as EmployeeQueries;
use percipiolondon\staff\gql\queries\PayRun as PayRunQueries;
use percipiolondon\staff\gql\queries\PayRunEntry as PayRunEntryQueries;
use percipiolondon\staff\gql\interfaces\elements\Employer as EmployerInterface;
use percipiolondon\staff\gql\interfaces\elements\Employee as EmployeeInterface;
use percipiolondon\staff\gql\interfaces\elements\PayRun as PayRunInterface;
use percipiolondon\staff\gql\interfaces\elements\PayRunEntry as PayRunEntryInterface;
use percipiolondon\staff\plugin\Services as StaffServices;
use percipiolondon\staff\variables\StaffVariable;

use yii\base\Event;

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
 * @since     0.2.0
 *
 * @property  Settings              $settings
 * @property  VitePluginService     $vite
 * @method    Settings              getSettings()
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
    public static $settings;

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
    public $schemaVersion = '1.0.0';

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
            // Register the vite service
            'vite' => [
                'class' => VitePluginService::class,
                'assetClass' => StaffAsset::class,
                'useDevServer' => true,
                'devServerPublic' => 'http://localhost:3050',
                'serverPublic' => 'http://localhost:8001',
                'errorEntry' => 'src/js/payrun.ts',
                'devServerInternal' => 'http://craft-staff-buildchain:3050',
                'checkDevServer' => true,
            ]
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
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        // Add in our console commands
        if (Craft::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'percipiolondon\staff\console\controllers';
        }

        // Initialize properties
        self::$settings = self::$plugin->getSettings();
        self::$view = Craft::$app->getView();

        $this->name = self::$settings->pluginName;

        $this->_setPluginComponents();

        $this->_registerGqlInterfaces();
        $this->_registerGqlSchemaComponents();

        $this->_registerGqlQueries();
        $this->_registerElementTypes();
        $this->_registerControllers();

        $this->installEventListeners();

//        Craft::dd(Staff::$plugin->payRuns->getTotalsById(1));

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
        return parent::getSettings();;
    }

    /**
     * @inheritdoc
     */
    public function getSettingsResponse()
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
                'url' => 'staff-management/dashboard'
            ];
        }
        if ($currentUser->can('hub:payruns')) {
            $subNavs['payRuns'] = [
                'label' => Craft::t('staff-management', 'Pay Runs'),
                'url' => 'staff-management/pay-runs'
            ];
        }

        $editableSettings = true;
        // Check against allowAdminChanges
        if ( !Craft::$app->getConfig()->getGeneral()->allowAdminChanges ) {
            $editableSettings = false;
        }

        if ($currentUser->can('hub:plugin-settings') && $editableSettings) {
            $subNavs['plugin'] = [
                'label' => Craft::t('staff-management', 'Plugin settings'),
                'url' => 'staff-management/plugin',
            ];
        }

        $navItem = array_merge($navItem, [
            'subnav' => $subNavs,
        ]);

        return $navItem;
    }

    // Protected Methods
    // =========================================================================

    /**
     * Install our event listeners.
     */
    protected function installEventListeners()
    {
        $request = Craft::$app->getRequest();
        $this->installGlobalEventListeners();
        // Install our event listeners
        if ($request->getIsCpRequest() && !$request->getIsConsoleRequest()) {
            $this->installCpEventListeners();
        }
    }

    /**
     * Install site event listeners for Control Panel requests only
     */
    protected function installCpEventListeners()
    {

        // Handler: UrlManager::EVENT_REGISTER_CP_URL_RULES
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
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
            function (RegisterUserPermissionsEvent $event) {
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
            'staff-management/plugin' => 'staff-management/settings/plugin',
            'staff-management/pay-runs' => 'staff-management/pay-run',
            'staff-management/pay-runs/queue' => 'staff-management/pay-run/get-queue',
            'staff-management/pay-runs/<employerId:\d+>' => 'staff-management/pay-run/pay-run-by-employer',
            'staff-management/pay-runs/fetch-pay-runs/<employerId:\d+>' => 'staff-management/pay-run/fetch-pay-runs',
            'staff-management/pay-runs/fetch-pay-run/<payRunId:\d+>' => 'staff-management/pay-run/fetch-pay-run',
            'staff-management/pay-runs/<employerId:\d+>/<payRunId:\d+>' => 'staff-management/pay-run/detail',
            'staff-management/pay-runs/download-template/<payRunId:\d+>' => 'staff-management/pay-run/download-template'
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
            'hub:payruns' => [
                'label' => Craft::t('staff-management', 'Pay Runs'),
            ],
            'hub:plugin-settings' => [
                'label' => Craft::t('staff-management', 'Edit Plugin Settings'),
            ]
        ];
    }

    /**
     * Install global event listeners for all request types
     */
    protected function installGlobalEventListeners()
    {
        // Handler: CraftVariable::EVENT_INIT
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function ( Event $event ) {
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

    private function _registerGqlInterfaces()
    {
        Event::on(
            Gql::class,
            Gql::EVENT_REGISTER_GQL_TYPES,
            function(RegisterGqlTypesEvent $event) {
                $event->types[] = EmployerInterface::class;
                $event->types[] = EmployeeInterface::class;
                $event->types[] = PayRunInterface::class;
                $event->types[] = PayRunEntryInterface::class;
            }
        );
    }

    private function _registerGqlSchemaComponents()
    {
        Event::on(
            Gql::class,
            Gql::EVENT_REGISTER_GQL_SCHEMA_COMPONENTS,
            function(RegisterGqlSchemaComponentsEvent $event) {

                $event->queries = array_merge($event->queries, [
                    'Staff Management' => [
                        // employers component with read action, labelled “View Employers” in UI
                        'employers:read' => ['label' => Craft::t('staff-management', 'View Employers')],
                        // employees component with read action, labelled “View Employees” in UI
                        'employees:read' => ['label' => Craft::t('staff-management', 'View Employees')],
                        // payruns component with read action, labelled “View Payruns” in UI
                        'payruns:read' => ['label' => Craft::t('staff-management', 'View Payruns')]
                    ],
                    'PayrunEntries' => [
                        // payruns entries component with read action, labelled “View Payruns” in UI
                        'payrunentries:read' => ['label' => Craft::t('staff-management', 'View Payrun Entries')]
                    ],
                ]);

            }
        );
    }

    private function _registerGqlQueries()
    {
        Event::on(
            Gql::class,
            Gql::EVENT_REGISTER_GQL_QUERIES,
            function(RegisterGqlQueriesEvent $event) {
                $event->queries = array_merge(
                    $event->queries,
                    EmployerQueries::getQueries(),
                    EmployeeQueries::getQueries(),
                    PayRunQueries::getQueries(),
                    PayRunEntryQueries::getQueries(),
                );
            }
        );
    }

    private function _registerElementTypes()
    {
        // Register our elements
        Event::on(
            Elements::class,
            Elements::EVENT_REGISTER_ELEMENT_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = EmployerElement::class;
                $event->types[] = EmployeeElement::class;
                $event->types[] = PayRunElement::class;
                $event->types[] = PayRunEntryElement::class;
            }
        );
    }

    private function _registerControllers()
    {
        // Add in our console commands
        if (Craft::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'percipiolondon\staff\console\controllers';
        }
    }
}
