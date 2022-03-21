<?php

namespace percipiolondon\staff\jobs;

use craft\helpers\App;
use craft\helpers\Json;
use craft\queue\BaseJob;
use percipiolondon\staff\records\Employer as EmployerRecord;
use percipiolondon\staff\Staff;
use percipiolondon\staff\helpers\Logger;
use Craft;

class FetchPayCodesListJob extends BaseJob
{
    public $criteria;

    public function execute($queue): void
    {
        $logger = new Logger();

        // connection props
        $api = App::parseEnv(Staff::$plugin->getSettings()->apiKeyStaffology);
        $base_url = 'https://api.staffology.co.uk/';
        $credentials = base64_encode('staff:'.$api);
        $headers = [
            'headers' => [
                'Authorization' => 'Basic ' . $credentials,
            ],
        ];

        $client = new \GuzzleHttp\Client();

        $logger->stdout("↧ Fetching pay codes of " . $this->criteria['employer']['name'] . '...', $logger::RESET);

        try {
            $employer =  is_int($this->criteria['employer']['id'] ?? null) ? EmployerRecord::findOne(['staffologyId' => $this->criteria['employer']['id'] ?? null]) : $this->criteria['employer'];
            $employerId = $employer['id'] ?? null;

            $response = $client->get($base_url.'employers/'.$employerId.'/paycodes', $headers);
            $payCodes = Json::decodeIfJson($response->getBody()->getContents(), true);

            $logger->stdout(" done" . PHP_EOL, $logger::FG_GREEN);

            Staff::$plugin->payRuns->fetchPayCodes($payCodes, $this->criteria['employer']);
        } catch (\Exception $e) {

            $logger->stdout(PHP_EOL, $logger::RESET);
            $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
            Craft::error($e->getMessage(), __METHOD__);

        }
    }
}
