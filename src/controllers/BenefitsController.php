<?php

namespace percipiolondon\staff\controllers;

use Craft;
use craft\web\Controller;
use percipiolondon\staff\elements\BenefitProvider;
use percipiolondon\staff\Staff;
use yii\web\Response;

class BenefitsController extends Controller
{
    /**
     * Benefit Providers display
     *
     * @return Response The rendered result
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\web\ForbiddenHttpException
     */
    public function actionBenefitsProvider(): Response
    {
        $this->requireLogin();

        $variables = [];

        $pluginName = Staff::$settings->pluginName;
        $templateTitle = Craft::t('staff-management', 'Benefits');

        $variables['controllerHandle'] = 'benefits';
        $variables['pluginName'] = Staff::$settings->pluginName;
        $variables['title'] = $templateTitle;
        $variables['docTitle'] = "{$pluginName} - {$templateTitle}";
        $variables['selectedSubnavItem'] = 'benefits';
        $variables['providers'] = BenefitProvider::findAll();

        // Render the template
        return $this->renderTemplate('staff-management/benefits/provider/index', $variables);
    }

    /**
     * Benefit Providers display
     *
     * @return Response The rendered result
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\web\ForbiddenHttpException
     */
    public function actionBenefitsProviderNew(): Response
    {
        $this->requireLogin();

        $variables = [];

        $pluginName = Staff::$settings->pluginName;
        $templateTitle = Craft::t('staff-management', 'Benefits');

        $variables['controllerHandle'] = 'benefits';
        $variables['pluginName'] = Staff::$settings->pluginName;
        $variables['title'] = $templateTitle;
        $variables['docTitle'] = "{$pluginName} - {$templateTitle}";
        $variables['selectedSubnavItem'] = 'benefits';
        $variables['provider'] = null;

        // Render the template
        return $this->renderTemplate('staff-management/benefits/provider/form', $variables);
    }

    /**
     * Group Benefits Provider Detail display
     *
     * @return Response The rendered result
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\web\ForbiddenHttpException
     */
    public function actionBenefitsProviderDetail(int $providerId): Response
    {
        $this->requireLogin();

        $variables = [];

        $pluginName = Staff::$settings->pluginName;
        $templateTitle = Craft::t('staff-management', 'Benefit Provider - '.$providerId);

        $variables['controllerHandle'] = 'group-benefits';
        $variables['pluginName'] = Staff::$settings->pluginName;
        $variables['title'] = $templateTitle;
        $variables['docTitle'] = "{$pluginName} - {$templateTitle}";
        $variables['selectedSubnavItem'] = 'groupBenefits';

        // Render the template
        return $this->renderTemplate('staff-management/group-benefits/index', $variables);
    }
}