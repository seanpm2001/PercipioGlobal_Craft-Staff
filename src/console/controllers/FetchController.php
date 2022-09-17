<?php

namespace percipiolondon\staff\console\controllers;

use Craft;
use craft\console\Controller;
use craft\helpers\App;
use craft\queue\QueueInterface;
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
     * Provide a specific tax year. If not provided, the current tax year will be used
     * @var string
     */
    public $taxYear = '*';

    /**
     * Provide an employer to fetch [required]
     * @var string
     */
    public $employer = '*';

    /**
     * @param string $actionID
     * @return int[]|string[]
     */
    public function options($actionID)
    {
        $options = parent::options($actionID);
        $options[] = 'taxYear';
        $options[] = 'employer';

        return $options;
    }

    public function actionEmployers()
    {
        $this->stdout('' . PHP_EOL, Console::RESET);
        $this->stdout('--------------------------------- Start fetching data from Staffology' . PHP_EOL, Console::FG_CYAN);
        $this->stdout('' . PHP_EOL, Console::RESET);

        Staff::$plugin->employers->fetchEmployers();

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

        Staff::$plugin->employees->fetchEmployees();

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

        Staff::$plugin->payRuns->fetchPayRuns();

        $this->_runQueue();

        $this->stdout('' . PHP_EOL, Console::RESET);
        $this->stdout('--------------------------------- Done fetching from Staffology' . PHP_EOL, Console::FG_CYAN);
        $this->stdout('' . PHP_EOL, Console::RESET);
    }

    /**
     * Fetch pay runs from an employer. If you want a specific tax year, you can provide the tax year. parameter examples: --employer='1234' --taxYear='2021'
     * e.g. staff-management/pay-run/fetch-pay-run-by-employer --employer="1234" --taxYear="2021"
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function actionPayRunByEmployer()
    {
        $this->stdout('' . PHP_EOL, Console::RESET);
        $this->stdout('--------------------------------- Start fetching data from Staffology' . PHP_EOL, Console::FG_CYAN);
        $this->stdout('' . PHP_EOL, Console::RESET);

        Staff::$plugin->payRuns->fetchPayRuns($this->employer, $this->taxYear);

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

        Staff::$plugin->payRunEntries->fetchPayRunEntries();

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
