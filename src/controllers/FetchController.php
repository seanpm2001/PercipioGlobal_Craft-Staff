<?php

namespace percipiolondon\staff\controllers;

use Craft;
use craft\helpers\Queue;
use craft\helpers\App;
use craft\web\Controller;
use craft\queue\QueueInterface;
use yii\queue\redis\Queue as RedisQueue;
use percipiolondon\staff\elements\Employer;
use percipiolondon\staff\elements\PayRun;
use percipiolondon\staff\jobs\FetchEmployeesJob;
use percipiolondon\staff\jobs\FetchEmployersJob;
use percipiolondon\staff\jobs\FetchPayRunEntriesJob;
use percipiolondon\staff\jobs\FetchPayRunsJob;
use percipiolondon\staff\Staff;
use yii\web\Response;

class FetchController extends Controller
{
    public function actionIndex(): Response
    {
        $variables = [];
        $pluginName = Staff::$plugin->settings->pluginName;
        $templateTitle = Craft::t('staff-management', 'Staffology Fetches');

        $variables['pluginName'] = Staff::$plugin->settings->pluginName;
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

        Queue::push(new FetchPayRunsJob([
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

    public function actionAll(): Response
    {
        $employers = Staff::$plugin->employers->fetchEmployerList();
        Staff::$plugin->employers->fetchEmployers($employers);

        // This might take a while
        App::maxPowerCaptain();
        $queue = Craft::$app->getQueue();
        if ($queue instanceof QueueInterface) {
            $queue->run();
        } elseif ($queue instanceof RedisQueue) {
            $queue->run(false);
        }

        return $this->redirect('staff-management/fetch');
    }
}