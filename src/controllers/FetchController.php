<?php

namespace percipiolondon\staff\controllers;

use Craft;
use craft\helpers\Queue;
use craft\web\Controller;
use percipiolondon\staff\elements\Employer;
use percipiolondon\staff\jobs\v2\FetchEmployeesJob;
use percipiolondon\staff\jobs\v2\FetchEmployersJob;
use percipiolondon\staff\jobs\v2\FetchPayRunJob;
use percipiolondon\staff\Staff;
use yii\base\BaseObject;
use yii\web\Response;

class FetchController extends Controller
{
    public function actionIndex(): Response
    {
        $variables = [];
        $pluginName = Staff::$settings->pluginName;
        $templateTitle = Craft::t('staff-management', 'Staffology Fetches');

        $variables['pluginName'] = Staff::$settings->pluginName;
        $variables['title'] = $templateTitle;
        $variables['docTitle'] = "{$pluginName} - {$templateTitle}";
        $variables['selectedSubnavItem'] = 'fetches';

        // Render the template
        return $this->renderTemplate('staff-management/fetch', $variables);
    }

    public function actionEmployer(): Response
    {
        Queue::push(new FetchEmployersJob([
            'description' => 'Fetching employers',
        ]));

        return $this->redirect('staff-management/fetch');
    }

    public function actionEmployee(): Response
    {
        Queue::push(new FetchEmployeesJob([
            'criteria' => [
                'employers' => Employer::findAll(),
            ],
            'description' => 'Fetching employees',
        ]));

        return $this->redirect('staff-management/fetch');
    }

    public function actionPayRun(): Response
    {
        Queue::push(new FetchPayRunJob([
            'criteria' => [
                'employers' => Employer::findAll(),
            ],
            'description' => 'Fetching pay run',
        ]));

        return $this->redirect('staff-management/fetch');
    }
}