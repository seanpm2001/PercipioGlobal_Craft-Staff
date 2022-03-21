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
use craft\elements\Entry;
use craft\helpers\UrlHelper;
use craft\web\Controller;

use yii\web\NotFoundHttpException;
use yii\web\Response;

use percipiolondon\staff\Staff;


class SettingsController extends Controller
{
    /**
     * Dashboard display
     *
     * @param string|null $siteHandle
     * @param bool        $showWelcome
     *
     * @return Response The rendered result
     * @throws NotFoundHttpException
     * @throws \yii\web\ForbiddenHttpException
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
     * Settings display
     *
     *
     * @return Response The rendered result
     * @throws NotFoundHttpException
     * @throws \yii\web\ForbiddenHttpException
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

    /**
     * Saves a plugin’s settings.
     *
     * @return Response|null
     * @throws NotFoundHttpException if the requested plugin cannot be found
     * @throws \yii\web\BadRequestHttpException
     * @throws \craft\errors\MissingComponentException
     */

    public function actionSavePluginSettings() {
        $this->requirePostRequest();
        $pluginHandle = Craft::$app->getRequest()->getRequiredBodyParam('pluginHandle');
        $plugin = Craft::$app->getPlugins()->getPlugin($pluginHandle);

        if ( $plugin === null ) {
            throw new NotFoundHttpExceptio('Plugin not found');
        }

        $settings = [
            'apiKeyStaffology' => Craft::$app->getRequest()->getBodyParam('apiKeyStaffology')
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
}