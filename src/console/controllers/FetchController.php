<?php

namespace percipiolondon\staff\console\controllers;

use Craft;
use craft\console\Controller;
use craft\helpers\App;
use craft\queue\QueueInterface;
use percipiolondon\staff\elements\Employer;
use percipiolondon\staff\elements\PayRun;
use percipiolondon\staff\jobs\v2\FetchEmployeesJob;
use percipiolondon\staff\jobs\v2\FetchEmployersJob;
use percipiolondon\staff\jobs\v2\FetchPayRunEntriesJob;
use percipiolondon\staff\jobs\v2\FetchPayRunJob;
use percipiolondon\staff\Staff;
use yii\helpers\Console;
use yii\queue\redis\Queue as RedisQueue;

/**
 * Class FetchController
 *
 * @package percipiolondon\staff\console\controllers
 */
class FetchController extends Controller
{
    /**
     * Fetch all the employers/employees/payruns/pensions/... from staffology
     * e.g.: actions/admin/staff-management/employer-controller/fetch
     */
    public function actionIndex()
    {
        $this->stdout("" . PHP_EOL, Console::RESET);
        $this->stdout("--------------------------------- Start fetching data from Staffology" . PHP_EOL, Console::FG_CYAN);
        $this->stdout("" . PHP_EOL, Console::RESET);

        //Fetch a list of all employers from Staffology
        $employers = Staff::$plugin->employers->fetchEmployerList();

        //Fetch all the standalone calls needed before fetching Employer / Employee / Pay Run
//        $this->stdout("" . PHP_EOL, Console::RESET);
//        Staff::$plugin->pensions->fetchPensionSchemes($employers);
//
        // Fetch Employer / Employee
        $this->stdout("" . PHP_EOL, Console::RESET);
        Staff::$plugin->employers->fetchEmployers($employers);

        $this->_runQueue();

        $this->stdout("" . PHP_EOL, Console::RESET);
        $this->stdout("--------------------------------- Done fetching from Staffology" . PHP_EOL, Console::FG_CYAN);
        $this->stdout("" . PHP_EOL, Console::RESET);
    }

    public function actionEmployers()
    {
        $this->stdout('' . PHP_EOL, Console::RESET);
        $this->stdout('--------------------------------- Start fetching data from Staffology' . PHP_EOL, Console::FG_CYAN);
        $this->stdout('' . PHP_EOL, Console::RESET);

        $queue = Craft::$app->getQueue();
        $queue->push(new FetchEmployersJob([
            'description' => 'Fetching employers',
        ]));

        $this->_runQueue();

        $this->stdout('' . PHP_EOL, Console::RESET);
        $this->stdout('--------------------------------- Done fetching from Staffology' . PHP_EOL, Console::FG_CYAN);
        $this->stdout('' . PHP_EOL, Console::RESET);
    }

    public function actionEmployees()
    {
        $this->stdout('' . PHP_EOL, Console::RESET);
        $this->stdout('--------------------------------- Start fetching data from Staffology' . PHP_EOL, Console::FG_CYAN);
        $this->stdout('' . PHP_EOL, Console::RESET);

        $queue = Craft::$app->getQueue();
        $queue->push(new FetchEmployeesJob([
            'criteria' => [
                'employers' => Employer::findAll(),
            ],
            'description' => 'Fetching employees',
        ]));

        $this->_runQueue();

        $this->stdout('' . PHP_EOL, Console::RESET);
        $this->stdout('--------------------------------- Done fetching from Staffology' . PHP_EOL, Console::FG_CYAN);
        $this->stdout('' . PHP_EOL, Console::RESET);
    }

    public function actionPayRun()
    {
        $this->stdout('' . PHP_EOL, Console::RESET);
        $this->stdout('--------------------------------- Start fetching data from Staffology' . PHP_EOL, Console::FG_CYAN);
        $this->stdout('' . PHP_EOL, Console::RESET);

        $queue = Craft::$app->getQueue();
        $queue->push(new FetchPayRunJob([
            'criteria' => [
                'employers' => Employer::findAll(),
            ],
            'description' => 'Fetching pay run',
        ]));

        $this->_runQueue();

        $this->stdout('' . PHP_EOL, Console::RESET);
        $this->stdout('--------------------------------- Done fetching from Staffology' . PHP_EOL, Console::FG_CYAN);
        $this->stdout('' . PHP_EOL, Console::RESET);
    }

    public function actionPayRunEntries()
    {
        $this->stdout('' . PHP_EOL, Console::RESET);
        $this->stdout('--------------------------------- Start fetching data from Staffology' . PHP_EOL, Console::FG_CYAN);
        $this->stdout('' . PHP_EOL, Console::RESET);

        $queue = Craft::$app->getQueue();
        $queue->push(new FetchPayRunEntriesJob([
            'criteria' => [
                'payRuns' => PayRun::findAll(),
            ],
            'description' => 'Fetching pay run entries',
        ]));

        $this->_runQueue();

        $this->stdout('' . PHP_EOL, Console::RESET);
        $this->stdout('--------------------------------- Done fetching from Staffology' . PHP_EOL, Console::FG_CYAN);
        $this->stdout('' . PHP_EOL, Console::RESET);
    }

    /**
     * Start the queue
     */
    private function _runQueue()
    {
        // This might take a while
        App::maxPowerCaptain();
        $queue = Craft::$app->getQueue();
        if ($queue instanceof QueueInterface) {
            $queue->run();
        } elseif ($queue instanceof RedisQueue) {
            $queue->run(false);
        }
    }
}
