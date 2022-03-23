<?php

namespace percipiolondon\staff\jobs;

use Craft;
use craft\helpers\App;
use craft\queue\BaseJob;
use percipiolondon\staff\helpers\Logger;
use percipiolondon\staff\records\Employee;
use percipiolondon\staff\records\PayRunEntry as PayRunEntryRecord;
use percipiolondon\staff\elements\PayRunEntry;
use craft\helpers\Queue;
use percipiolondon\staff\Staff;

class CreatePayRunEntryJob extends Basejob
{
    public $criteria;

    public function execute($queue): void
    {
        $logger = new Logger();

        $api = App::parseEnv(Staff::$plugin->getSettings()->apiKeyStaffology);
        $credentials = base64_encode('staff:'.$api);
        $headers = [
            'headers' => [
                'Authorization' => 'Basic ' . $credentials,
            ],
        ];
        $client = new \GuzzleHttp\Client();

        $current = 0;
        $total = count($this->criteria['payRunEntries']);

        foreach($this->criteria['payRunEntries'] as $payRunEntryData) {

            $current++;
            $progress = "[".$current."/".$total."] ";

            $logger->stdout($progress."↧ Fetching pay run entry of " . $payRunEntryData['name'] . '...', $logger::RESET);
            $logger->stdout(" done" . PHP_EOL, $logger::FG_GREEN);

            $base_url = "https://api.staffology.co.uk/" . $payRunEntryData['url'];

            try {
                $response = $client->get($base_url, $headers);
                $result = json_decode($response->getBody()->getContents(), true);

                Staff::$plugin->payRuns->savePayRunEntry($result, $this->criteria['employer'], $this->criteria['payRun']->id);

                if(!App::parseEnv('$HUB_DEV_MODE')) {
                    Staff::$plugin->payRuns->fetchPaySlip($result, $this->criteria['employer']);
                }
            } catch (\Exception $e) {

                $logger->stdout(PHP_EOL, $logger::RESET);
                $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
                Craft::error($e->getMessage(), __METHOD__);

            }
        }
    }

    protected function defaultDescription(): string
    {
        return sprintf(
            'Fetching Pay Run Entries from "%s"',
            $this->criteria['employer']['id']
        );
    }
}
