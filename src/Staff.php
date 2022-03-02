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
use craft\events\RegisterGqlSchemaComponentsEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterGqlQueriesEvent;
use craft\events\RegisterGqlTypesEvent;
use craft\services\Elements;
use craft\services\Gql;

use nystudio107\pluginvite\services\VitePluginService;

use percipiolondon\staff\services\Employers as EmployersService;
use percipiolondon\staff\services\Employees as EmployeesService;
use percipiolondon\staff\services\PayRun as PayRunService;
use percipiolondon\staff\services\PayRunEntries as PayRunEntriesService;
use percipiolondon\staff\services\Pensions as PensionsService;

use percipiolondon\staff\assetbundles\staff\StaffCsvAsset;
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
 * @property  EmployersService      $employers
 * @property  EmployeesService      $employees
 * @property  PayRunService         $payRun
 * @property  PayRunEntriesService  $payRunEntries
 * @property  PensionsService       $pensions
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
                'assetClass' => StaffCsvAsset::class,
                'useDevServer' => true,
                'devServerPublic' => 'http://localhost:3050',
                'serverPublic' => 'http://localhost:8001',
                'errorEntry' => 'src/js/csv.ts',
                'devServerInternal' => 'http://craft-staff-buildchain:3050',
                'checkDevServer' => true,
            ]
        ];

        parent::__construct($id, $parent, $config);
    }

    // Public Properties
    // =========================================================================

    /**
     * To execute your plugin’s migrations, you’ll need to increase its schema version.
     *
     * @var string
     */
    public $schemaVersion = '0.2.0';

    /**
     * Set to `true` if the plugin should have a settings view in the control panel.
     *
     * @var bool
     */
    public $hasCpSettings = true;

    /**
     * Set to `true` if the plugin should have its own section (main nav item) in the control panel.
     *
     * @var bool
     */
    public $hasCpSection = true;

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
        $this->_setPluginComponents();

        $this->_registerGqlInterfaces();
        $this->_registerGqlSchemaComponents();

        $this->_registerGqlQueries();
        $this->_registerElementTypes();
        $this->_registerControllers();

        $this->installEventListeners();

        // Do something after we're installed
//        Event::on(
//            Plugins::class,
//            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
//            function (PluginEvent $event) {
//                if ($event->plugin === $this) {
//                    // We were just installed
//                }
//            }
//        );

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

    // Protected Methods
    // =========================================================================

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
     * Returns the rendered settings HTML, which will be inserted into the content
     * block on the settings page.
     *
     * @return string The rendered settings HTML
     */
    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate(
            'staff-management/settings',
            [
                'settings' => $this->getSettings()
            ]
        );
    }

    /**
     * Install our event listeners.
     */
    protected function installEventListeners()
    {
        $this->installGlobalEventListeners();
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
                   'class' => ViteVariable::class,
                   'viteService' => $this->vite,
               ]);
            }
        );
    }


    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function getCpNavItem(): array
    {
        $nav = parent::getCpNavItem();

        $nav['label'] = Craft::t('staff-management', 'Staff Management');

        $nav['subnav']['dashboard'] = [
            'label' => Craft::t('staff-management', 'Dashboard'),
            'url' => 'staff-management'
        ];

        if (Craft::$app->getUser()->checkPermission('companies-manageCompanies')) {
            $nav['subnav']['employers'] = [
                'label' => Craft::t('staff-management', 'Employers'),
                'url' => 'staff-management/employers'
            ];

            $nav['subnav']['employees'] = [
                'label' => Craft::t('staff-management', 'Employees'),
                'url' => 'staff-management/employees'
            ];

            $nav['subnav']['payrun'] = [
                'label' => Craft::t('staff-management', 'Pay Run'),
                'url' => 'staff-management/payrun'
            ];
        }

        return $nav;
    }


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
                        // payrun entries component with read action, labelled “View Payruns” in UI
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
                $event->types[] = HardingUserElement::class;
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
