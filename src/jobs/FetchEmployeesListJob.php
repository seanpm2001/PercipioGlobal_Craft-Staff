<?php

namespace percipiolondon\staff\jobs;

use Craft;
use craft\helpers\App;
use craft\helpers\Json;
use craft\queue\BaseJob;
use percipiolondon\staff\helpers\Logger;
use percipiolondon\staff\Staff;

class FetchEmployeesListJob extends BaseJob
{
    public $criteria;

    public function execute($queue): void
    {
        $logger = new Logger();

        $logger->stdout("↧ Fetching employees of " . $this->criteria['employer']['name'] . '...', $logger::RESET);

        // connection props
        $api = App::parseEnv(Staff::$plugin->getSettings()->apiKeyStaffology);
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

            $logger->stdout(" done" . PHP_EOL, $logger::FG_GREEN);

            foreach ($employees as $employee) {
                $currentEmployee++;

                Staff::$plugin->employees->fetchEmployee($employee, $this->criteria['employer']);
//                Staff::$plugin->pensions->fetchPension($employee, $this->criteria['employer']['id']);

                if ($currentEmployee === $totalEmployees) {
                    Staff::$plugin->payRuns->fetchPayRunByStaffologyEmployer($this->criteria['employer']);
                }

                $this->setProgress($queue, $currentEmployee / $totalEmployees);
            }
        } catch (\Exception $e) {
            $logger->stdout(PHP_EOL, $logger::RESET);
            $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
            Craft::error($e->getMessage(), __METHOD__);
        }
    }
}
