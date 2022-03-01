<?php

namespace percipiolondon\staff\jobs;

use craft\helpers\App;
use craft\helpers\Json;
use craft\queue\BaseJob;
use percipiolondon\staff\Staff;
use percipiolondon\staff\helpers\Logger;
use Craft;

class FetchPaySchedulesJob extends BaseJob
{
    public $criteria;

    public function execute($queue): void
    {
        $logger = new Logger();

        // connection props
        $api = App::parseEnv(Staff::$plugin->getSettings()->staffologyApiKey);
        $base_url = 'https://api.staffology.co.uk/';
        $credentials = base64_encode('staff:'.$api);
        $headers = [
            'headers' => [
                'Authorization' => 'Basic ' . $credentials,
            ],
        ];

        $client = new \GuzzleHttp\Client();
        $date = date("Y");

        $base_url = "https://api.staffology.co.uk/employers/{$this->criteria['employer']['id']}/schedules/Year{$date}";

        $logger->stdout("↧ Fetching pay schedules of " . $this->criteria['employer']['name'] . '...', $logger::RESET);

        try {
            $logger->stdout(" done" . PHP_EOL, $logger::FG_GREEN);

            $response = $client->get($base_url, $headers);
            $paySchedules = json_decode($response->getBody()->getContents(), true);

            Staff::$plugin->payRun->fetchPayRun($paySchedules, $this->criteria['employer']);

        } catch (\Exception $e) {

            $logger->stdout(PHP_EOL, $logger::RESET);
            $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
            Craft::error($e->getMessage(), __METHOD__);

        }
    }
}
