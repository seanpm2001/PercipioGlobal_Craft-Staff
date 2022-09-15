<?php

namespace percipiolondon\staff\jobs\v2;

use Craft;
use craft\helpers\App;
use craft\helpers\Json;
use craft\queue\BaseJob;
use percipiolondon\staff\helpers\Logger;
use percipiolondon\staff\Staff;

class FetchEmployeesJob extends BaseJob
{
    public array $criteria = [];

    public function execute($queue)
    {
        $logger = new Logger();
        $api = App::parseEnv(Staff::$plugin->getSettings()->apiKeyStaffology);
        $credentials = base64_encode('staff:' . $api);
        $headers = [
            'headers' => [
                'Authorization' => 'Basic ' . $credentials,
            ],
        ];
        $client = new \GuzzleHttp\Client();

        foreach ($this->criteria['employers'] as $employer) {
            $base_url = Staff::$plugin->getSettings()->apiBaseUrl . 'employers/' . $employer->staffologyId . '/employees';

            try {
                $response = $client->get($base_url, $headers);
                $employees = Json::decodeIfJson($response->getBody()->getContents(), true);

                //Delete existing if they don't exist on Staffology anymore
                Staff::$plugin->employees->syncEmployees($employer, $employees);

                foreach ($employees as $i => $employee) {
                    $employeeName = $employee['name'];
                    $logger->stdout('↧ Fetch employee info from ' . $employeeName, $logger::RESET);

                    try {
                        $logger->stdout(' done' . PHP_EOL, $logger::FG_GREEN);

                        $response = $client->get($employee['url'], $headers);
                        $employee = Json::decodeIfJson($response->getBody()->getContents(), true);

                        Staff::$plugin->employees->saveEmployee($employee, $employeeName, $employer);
                    } catch (\Exception $e) {
                        $logger->stdout(PHP_EOL, $logger::RESET);
                        $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
                        Craft::error($e->getMessage(), __METHOD__);
                    }

                    $this->setProgress(
                        $queue,
                        $i / count($employees),
                        Craft::t('staff-management', 'Employees fetch from ' . $employer->name . ': {step, number} of {total, number}', [
                            'step' => $i + 1,
                            'total' => count($employees),
                        ])
                    );
                }

            } catch (\Exception $e) {
                $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
                Craft::error($e->getMessage() . ': thrown with API key: ' . $api, __METHOD__);
            }
        }
    }
}