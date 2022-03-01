<?php

namespace percipiolondon\staff\jobs;

use craft\helpers\App;
use craft\helpers\Json;
use craft\queue\BaseJob;
use percipiolondon\staff\Staff;
use percipiolondon\staff\helpers\Logger;
use Craft;

class FetchEmployeesListJob extends BaseJob
{
    public $criteria;

    public function execute($queue): void
    {
        $logger = new Logger();

        $logger->stdout($this->criteria['progress']['label']."↧ Fetching employees of " . $this->criteria['employer']['name'] . '...', $logger::RESET);

        // connection props
        $api = App::parseEnv(Staff::$plugin->getSettings()->staffologyApiKey);
        $base_url = 'https://api.staffology.co.uk/';
        $credentials = base64_encode('staff:' . $api);
        $headers = [
            'headers' => [
                'Authorization' => 'Basic ' . $credentials,
            ],
        ];

        $client = new \GuzzleHttp\Client();

        try {
            $response = $client->get($base_url . 'employers/' . $this->criteria['employer']['id'] . '/employees', $headers);
            $employees = Json::decodeIfJson($response->getBody()->getContents(), true);

            $currentEmployee = 0;
            $totalEmployees = count($employees);

            foreach($employees as $employee) {

                $currentEmployee++;
                $progress = "[".$currentEmployee."/".$totalEmployees."] ";

                Staff::$plugin->employees->fetchEmployee($employee, $progress);
                Staff::$plugin->pensions->fetchPension($employee, $this->criteria['employer']['id'], $progress);

                if(
                    $this->criteria['progress']['current'] === $this->criteria['progress']['total'] &&
                    $currentEmployee === $totalEmployees
                ){
                    Staff::$plugin->payRun->fetchPayRunSchedule($this->criteria['employer']);
                }

                $this->setProgress($queue, $currentEmployee / $totalEmployees);


            }

        } catch (\Exception $e) {

            $logger->stdout(PHP_EOL, $logger::RESET);
            $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
            Craft::error($e->getMessage(), __METHOD__);

        }

        $logger->stdout(" done" . PHP_EOL, $logger::FG_GREEN);

//        $logger->stdout("" . PHP_EOL, $logger::RESET);
//        $logger->stdout("--------- Employees ---------" . PHP_EOL, $logger::RESET);
//        Staff::$plugin->employees->fetchEmployees($employees);
    }
}
