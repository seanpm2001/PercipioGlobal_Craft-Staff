<?php

namespace percipiolondon\staff\jobs;

use craft\helpers\App;
use craft\helpers\Json;
use craft\queue\BaseJob;
use percipiolondon\staff\Staff;
use percipiolondon\staff\helpers\Logger;
use Craft;

class CreateEmployeeJob extends BaseJob
{
    public $criteria;

    public function execute($queue): void
    {
        $logger = new Logger();

        // connection props
        $api = App::parseEnv(Staff::$plugin->getSettings()->apiKeyStaffology);
        $credentials = base64_encode('staff:'.$api);
        $headers = [
            'headers' => [
                'Authorization' => 'Basic ' . $credentials,
            ],
        ];

        $client = new \GuzzleHttp\Client();

        $logger->stdout("↧ Fetch employee info from ".$this->criteria['employee']['name'], $logger::RESET);

        try {

            $response = $client->get($this->criteria['employee']['url'], $headers);
            $employee = Json::decodeIfJson($response->getBody()->getContents(), true);

            $logger->stdout(" done" . PHP_EOL, $logger::FG_GREEN);

            Staff::$plugin->employees->saveEmployee($employee, $this->criteria['employee']['name']);

        } catch (\Exception $e) {

            $logger->stdout(PHP_EOL, $logger::RESET);
            $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
            Craft::error($e->getMessage(), __METHOD__);

        }
    }
}
