<?php

namespace percipiolondon\staff\jobs;

use Craft;
use craft\helpers\App;
use craft\helpers\Json;
use craft\queue\BaseJob;
use percipiolondon\staff\helpers\Logger;
use percipiolondon\staff\Staff;

class CreatePensionJob extends BaseJob
{
    public $criteria;

    public function execute($queue): void
    {
        $logger = new Logger();

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

        $logger->stdout("↧ Fetching pension info of " . $this->criteria['employee']['name'] . '...', $logger::RESET);

        try {
            $logger->stdout(" done" . PHP_EOL, $logger::FG_GREEN);

            $response = $client->get($base_url . 'employers/' . $this->criteria['employer'] . '/employees/' . $this->criteria['employee']['id'] . '/pension', $headers);
            $results = Json::decodeIfJson($response->getBody()->getContents(), true);

            Staff::$plugin->pensions->savePension($results);
        } catch (\Exception $e) {
            if ($e->getCode() === 404) {
                $logger->stdout('There was no pension found for ' . $this->criteria['employee']['name'] . PHP_EOL, $logger::FG_PURPLE);
                Craft::warning($e->getMessage(), __METHOD__);
            } else {
                $logger->stdout(PHP_EOL, $logger::RESET);
                $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
                Craft::error($e->getMessage(), __METHOD__);
            }
        }
    }
}
