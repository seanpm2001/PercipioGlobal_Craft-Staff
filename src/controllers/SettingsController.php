<?php
/**
 * Staff Management plugin for Craft CMS 3.x
 *
 * @link      https://percipio.london/
 * @copyright Copyright (c) 2022 Percipio Global Ltd.
 * @license   https://percipio.london/license
 */

namespace percipiolondon\staff\controllers;

use Craft;
use craft\errors\MissingComponentException;
use craft\helpers\App;
use craft\web\Controller;

use percipiolondon\staff\elements\SettingsAdmin;
use percipiolondon\staff\records\Settings;
use percipiolondon\staff\Staff;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

use yii\web\Response;

class SettingsController extends Controller
{
    /**
     * Dashboard display
     *
     * @param string|null $siteHandle
     *
     * @return Response The rendered result
     * @throws NotFoundHttpException
     * @throws ForbiddenHttpException
     */
    public function actionGroupBenefits(string $siteHandle = null): Response
    {
        $variables = [];

        $pluginName = Staff::$settings->pluginName;
        $templateTitle = Craft::t('staff-management', 'Group BenefitProvider');

        $variables['controllerHandle'] = 'group-benefits';
        $variables['pluginName'] = Staff::$settings->pluginName;
        $variables['title'] = $templateTitle;
        $variables['docTitle'] = "{$pluginName} - {$templateTitle}";
        $variables['selectedSubnavItem'] = 'group-benefits';

        // Render the template
        return $this->renderTemplate('staff-management/group-benefits/index', $variables);
    }

    /**
     * Dashboard display
     *
     * @param string|null $siteHandle
     *
     * @return Response The rendered result
     * @throws NotFoundHttpException
     * @throws ForbiddenHttpException
     */
    public function actionDashboard(string $siteHandle = null): Response
    {
        $variables = [];

        $pluginName = Staff::$settings->pluginName;
        $templateTitle = Craft::t('staff-management', 'Dashboard');

        $variables['controllerHandle'] = 'dashboard';
        $variables['pluginName'] = Staff::$settings->pluginName;
        $variables['title'] = $templateTitle;
        $variables['docTitle'] = "{$pluginName} - {$templateTitle}";
        $variables['selectedSubnavItem'] = 'dashboard';

        // Render the template
        return $this->renderTemplate('staff-management/dashboard/index', $variables);
    }

    /**
     * Payruns display
     *
     * @param string|null $siteHandle
     *
     * @return Response The rendered result
     * @throws NotFoundHttpException
     * @throws ForbiddenHttpException
     */
    public function actionPayRuns(string $siteHandle = null): Response
    {
        $variables = [];

        $pluginName = Staff::$settings->pluginName;
        $templateTitle = Craft::t('staff-management', 'Pay runs');

        $variables['controllerHandle'] = 'pay-runs';
        $variables['pluginName'] = Staff::$settings->pluginName;
        $variables['title'] = $templateTitle;
        $variables['docTitle'] = "{$pluginName} - {$templateTitle}";
        $variables['selectedSubnavItem'] = 'pay-runs';

        // Render the template
        return $this->renderTemplate('staff-management/payruns/index', $variables);
    }

    /**
     * Settings display
     *
     *
     * @return Response The rendered result
     * @throws NotFoundHttpException
     * @throws ForbiddenHttpException
     */
    public function actionPlugin(): Response
    {
        $variables = [];
        $pluginName = Staff::$settings->pluginName;
        $templateTitle = Craft::t('staff-management', 'Plugin Settings');

        $variables['fullPageForm'] = true;
        $variables['pluginName'] = Staff::$settings->pluginName;
        $variables['title'] = $templateTitle;
        $variables['docTitle'] = "{$pluginName} - {$templateTitle}";
        $variables['selectedSubnavItem'] = 'plugin';
        $variables['settings'] = Staff::$settings;

        // Render the template
        return $this->renderTemplate('staff-management/settings/hub-settings', $variables);
    }

    public function actionUserSettings(): Response
    {
        $variables = [];
        $pluginName = Staff::$settings->pluginName;
        $templateTitle = Craft::t('staff-management', 'User Settings');

        $settings = Settings::find()->all();
        $currentSettings = SettingsAdmin::getSettingsAdminIds();

        $variables['fullPageForm'] = true;
        $variables['pluginName'] = Staff::$settings->pluginName;
        $variables['title'] = $templateTitle;
        $variables['docTitle'] = "{$pluginName} - {$templateTitle}";
        $variables['selectedSubnavItem'] = 'user-settings';
        $variables['settings'] = $settings;
        $variables['currentSettings'] = $currentSettings;

        // Render the template
        return $this->renderTemplate('staff-management/settings/user-settings', $variables);
    }

    /**
     * @throws BadRequestHttpException
     */
    public function actionSaveUserSettings(): Response
    {
        $this->requirePostRequest();

        $request = Craft::$app->getRequest();
        $userId = $request->getBodyParam('userId');
        $settings = $request->getBodyParam('settings');

        Staff::$plugin->staffSettings->setSettingsAdmin($settings, $userId);

        return $this->redirectToPostedUrl();
    }

    /**
     * Saves a plugin’s settings.
     *
     * @return Response|null
     * @throws NotFoundHttpException if the requested plugin cannot be found
     * @throws BadRequestHttpException
     * @throws MissingComponentException
     */

    public function actionSavePluginSettings()
    {
        $this->requirePostRequest();
        $pluginHandle = Craft::$app->getRequest()->getRequiredBodyParam('pluginHandle');
        $plugin = Craft::$app->getPlugins()->getPlugin($pluginHandle);

        if ($plugin === null) {
            throw new NotFoundHttpException('Plugin not found');
        }

        $settings = [
            'apiKeyStaffology' => Craft::$app->getRequest()->getBodyParam('apiKeyStaffology'),
        ];

        if (!Craft::$app->getPlugins()->savePluginSettings($plugin, $settings)) {
            Craft::$app->getSession()->setError(Craft::t('app', "Couldn't save plugin settings."));

            // Send the plugin back to the template
            Craft::$app->getUrlManager()->setRouteParams([
                'plugin' => $plugin,
            ]);

            return null;
        }

        Craft::$app->getSession()->setNotice(Craft::t('app', 'Plugin settings saved.'));

        return $this->redirectToPostedUrl();
    }

    public function actionGetGqlToken(): Response
    {
        $this->requireLogin();
        $this->requireAcceptsJson();

        $token = App::env('GQL_TOKEN') ?: null;

        return $this->asJson([
            'success' => true,
            'token' => $token,
        ]);
    }
}
