<?php

namespace percipiolondon\staff\controllers;

use Craft;
use craft\helpers\Queue;
use craft\web\Controller;
use percipiolondon\staff\elements\Employer;
use percipiolondon\staff\elements\PayRun;
use percipiolondon\staff\jobs\v2\FetchEmployeesJob;
use percipiolondon\staff\jobs\v2\FetchEmployersJob;
use percipiolondon\staff\jobs\v2\FetchPayRunEntriesJob;
use percipiolondon\staff\jobs\v2\FetchPayRunJob;
use percipiolondon\staff\Staff;
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

    public function actionEmployers(): Response
    {
        $this->requireLogin();
        $this->requireAcceptsJson();

        Queue::push(new FetchEmployersJob([
            'description' => 'Fetching employers',
        ]));

        return $this->asJson([
            'success' => true,
        ]);
    }

    public function actionEmployees(): Response
    {
        $this->requireLogin();
        $this->requireAcceptsJson();

        Queue::push(new FetchEmployeesJob([
            'criteria' => [
                'employers' => Employer::findAll(),
            ],
            'description' => 'Fetching employees',
        ]));

        return $this->asJson([
            'success' => true,
        ]);
    }

    public function actionPayRuns(): Response
    {
        $this->requireLogin();
        $this->requireAcceptsJson();

        Queue::push(new FetchPayRunJob([
            'criteria' => [
                'employers' => Employer::findAll(),
            ],
            'description' => 'Fetching pay run',
        ]));

        return $this->asJson([
            'success' => true,
        ]);
    }

    public function actionPayRunEntries(): Response
    {
        $this->requireLogin();
        $this->requireAcceptsJson();

        Queue::push(new FetchPayRunEntriesJob([
            'criteria' => [
                'payRuns' => PayRun::findAll(),
            ],
            'description' => 'Fetching pay run entries',
        ]));

        return $this->asJson([
            'success' => true,
        ]);
    }
}