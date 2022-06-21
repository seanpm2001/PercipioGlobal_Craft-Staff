<?php

namespace percipiolondon\staff\controllers;

use Craft;
use craft\web\Controller;
use percipiolondon\staff\Staff;
use yii\web\Response;

class GroupBenefitsController extends Controller
{
    /**
     * Group Benefits display
     *
     * @return Response The rendered result
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\web\ForbiddenHttpException
     */
    public function actionIndex(): Response
    {
        $this->requireLogin();

        $variables = [];

        $pluginName = Staff::$settings->pluginName;
        $templateTitle = Craft::t('staff-management', 'Benefits');

        $variables['controllerHandle'] = 'group-benefits';
        $variables['pluginName'] = Staff::$settings->pluginName;
        $variables['title'] = $templateTitle;
        $variables['docTitle'] = "{$pluginName} - {$templateTitle}";
        $variables['selectedSubnavItem'] = 'benefits';

        // Render the template
        return $this->renderTemplate('staff-management/benefits/group', $variables);
    }
}