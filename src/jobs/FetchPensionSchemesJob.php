<?php

namespace percipiolondon\staff\jobs;

use craft\helpers\App;
use craft\helpers\Json;
use craft\queue\BaseJob;
use percipiolondon\staff\helpers\Logger;
use percipiolondon\staff\Staff;

class FetchPensionSchemesJob extends BaseJob
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

        $currentPensionScheme = 0;
        $totalPensionScheme = count($this->criteria['employers']);

        foreach ($this->criteria['employers'] as $employer) {
            $currentPensionScheme++;
            $progress = "[" . $currentPensionScheme . "/" . $totalPensionScheme . "] ";

            $logger->stdout($progress . "↧ Fetching pension schemes of " . $employer['name'] . '...', $logger::RESET);
            $logger->stdout(" done" . PHP_EOL, $logger::FG_GREEN);

            $response = $client->get($base_url . 'employers/' . $employer['id'] . '/pensionschemes', $headers);
            $results = Json::decodeIfJson($response->getBody()->getContents(), true);

            //@TODO: save
            Staff::$plugin->pensions->savePensionScheme($results);

            $this->setProgress($queue, $currentPensionScheme / $totalPensionScheme);
        }
    }
}
