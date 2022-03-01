<?php

namespace percipiolondon\staff\console\controllers;

use craft\console\Controller;
use craft\helpers\App;
use craft\queue\QueueInterface;
use percipiolondon\staff\Staff;
use yii\helpers\Console;
use yii\queue\redis\Queue as RedisQueue;
use Craft;

class FetchController extends Controller
{
    public function actionIndex()
    {
        $this->stdout("" . PHP_EOL, Console::RESET);
        $this->stdout("--------------------------------- Start fetching data from Staffology" . PHP_EOL, Console::FG_CYAN);
        $this->stdout("" . PHP_EOL, Console::RESET);

        //Fetch a list of all employers from Staffology
        $employers = Staff::$plugin->employers->fetchEmployerList();

        //Fetch all the standalone calls needed for Employer / Employee / Pay Run
        $this->stdout("" . PHP_EOL, Console::RESET);
        Staff::$plugin->pensions->fetchPensionSchemes($employers);
        Staff::$plugin->payRun->fetchPayCodesList($employers);

        // Fetch Employer / Employee
        $this->stdout("" . PHP_EOL, Console::RESET);
        Staff::$plugin->employers->fetchEmployers($employers);

        // PROVIDE THESE AS JOBS
//        $this->stdout("" . PHP_EOL, Console::RESET);
//        $this->stdout("------ Pension Schemes ------" . PHP_EOL, Console::RESET);
//        Staff::$plugin->pensions->fetchPensionSchemes($employers);
//
//
//        $this->stdout("" . PHP_EOL, Console::RESET);
//        $this->stdout("--------- Employees list ---------" . PHP_EOL, Console::RESET);
//        Staff::$plugin->employees->fetch($employers, $this);


//        $this->stdout("" . PHP_EOL, Console::RESET);
//        $this->stdout("--------- Employees ---------" . PHP_EOL, Console::RESET);
//        Staff::$plugin->employees->fetchEmployees($employees, $this);


        $this->_runQueue();

        $this->stdout("" . PHP_EOL, Console::RESET);
        $this->stdout("--------------------------------- Done fetching from Staffology" . PHP_EOL, Console::FG_CYAN);
        $this->stdout("" . PHP_EOL, Console::RESET);

    }

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