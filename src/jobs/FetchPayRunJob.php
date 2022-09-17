<?php

namespace percipiolondon\staff\jobs;

use Craft;
use craft\helpers\App;
use craft\helpers\Json;
use craft\queue\BaseJob;
use percipiolondon\staff\helpers\Logger;
use percipiolondon\staff\Staff;
use percipiolondon\staff\records\PayRunEntry as PayRunEntryRecord;

class FetchPayRunJob extends BaseJob
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

        try {

            $employer = $this->criteria['employer'];
            $payRun = $this->criteria['payRun'];

            // fetch pay code
            try {
                $logger->stdout('↧ Fetching pay codes for ' . $employer->name . '...', $logger::RESET);
                $response = $client->get(Staff::$plugin->getSettings()->apiBaseUrl . 'employers/' . $employer->staffologyId . '/paycodes', $headers);
                $payCodes = Json::decodeIfJson($response->getBody()->getContents(), true);

                $fetchedPayCodes = [];
                foreach ($payCodes as $payCode) {
                    try {
                        $response = $client->get($payCode['url'], $headers);
                        $result = Json::decodeIfJson($response->getBody()->getContents(), true);

                        $fetchedPayCodes[] = $result;
                    } catch (\Exception $e) {
                        $logger->stdout(PHP_EOL, $logger::RESET);
                        $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
                        Craft::error($e->getMessage() . ': thrown with API key: ' . $api, __METHOD__);
                    }
                }

                $logger->stdout('done' . PHP_EOL, $logger::FG_GREEN);

                //Delete existing if they don't exist on Staffology anymore
                Staff::$plugin->payRuns->syncPayCode($employer, $fetchedPayCodes);

                //Save pay codes
                foreach($fetchedPayCodes as $payCode) {
                    Staff::$plugin->payRuns->savePayCode($payCode, $employer);
                }

            } catch (\Exception $e) {
                $logger->stdout(PHP_EOL, $logger::RESET);
                $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
                Craft::error($e->getMessage() . ': thrown with API key: ' . $api, __METHOD__);
            }

            // fetch pay run
            $url = Staff::$plugin->getSettings()->apiBaseUrl . 'employers/' . $employer['staffologyId'] . '/payrun/' . $payRun['taxYear'] . '/' . $payRun['payPeriod'] . '/' . $payRun['period'];
            try {

                $response = $client->get($url, $headers);
                $payRunData = Json::decode($response->getBody()->getContents(), true);

                if ($payRunData) {

                    $logger->stdout('--- Start pay run entries fetching for ' . ($employer->name ?? '-') . ' [' . $payRunData['taxYear'] . '/'  .$payRunData['taxMonth'] . ']' . PHP_EOL, $logger::RESET);

                    foreach ($payRunData['entries'] as $j => $payRunEntryData) {
                        $logger->stdout('Pay run entry [' . $j+1 . '/' . count($payRunData['entries']) . '] ↧ Fetching pay run entry of ' . $payRunEntryData['name'] . '...', $logger::RESET);
                        $logger->stdout(' done' . PHP_EOL, $logger::FG_GREEN);

                        $url = Staff::$plugin->getSettings()->apiBaseUrl . $payRunEntryData['url'];

                        try {
                            $response = $client->get($url, $headers);
                            $result = Json::decode($response->getBody()->getContents(), true);

                            $payRunEntry = Staff::$plugin->payRunEntries->savePayRunEntry($result, $employer->toArray(), $payRun->id);

                            if (!App::parseEnv('$HUB_DEV_MODE') && $payRunEntry && $payRunEntry->state === 'Finalised' && $payRunEntry->pdf === '') {
                                try {
                                    $headers = [
                                        'headers' => [
                                            'Authorization' => 'Basic ' . $credentials,
                                            'Accept' => 'application/pdf',
                                        ],
                                    ];
                                    $url = Staff::$plugin->getSettings()->apiBaseUrl . 'employers/' . $employer->staffologyId . '/reports/' . $payRunEntry->taxYear . '/' . $payRunEntry->payPeriod . '/' . $payRunEntry->period . '/' . $payRunEntry->staffologyId . '/payslip';
                                    $response = $client->get($url, $headers);
                                    $result = Json::decode($response->getBody()->getContents(), true);

                                    if ($result) {
                                        $paySlip = Json::decodeIfJson($result, true);
                                        $payRunEntry = PayRunEntryRecord::findOne($payRunEntry['id']);
                                        Staff::$plugin->payRunEntries->savePaySlip($paySlip, $payRunEntry);
                                    }
                                } catch (\Exception $e) {
                                    $logger->stdout(PHP_EOL, $logger::RESET);
                                    $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
                                    Craft::error($e->getMessage(), __METHOD__);
                                }
                            }
                        } catch (\Exception $e) {
                            $logger->stdout(PHP_EOL, $logger::RESET);
                            $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
                            Craft::error($e->getMessage() . ': thrown with API key: ' . $api, __METHOD__);
                        }
                    }
                }

                $logger->stdout('--- End pay run entries fetching for ' . ($employer->name ?? '-') . ' [' . $payRunData['taxYear'] . '/'  .$payRunData['taxMonth'] . ']' . PHP_EOL, $logger::RESET);
            } catch (\Exception $e) {
                $logger->stdout(PHP_EOL, $logger::RESET);
                $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
                Craft::error($e->getMessage() . ': thrown with API key: ' . $api, __METHOD__);
            }


        } catch (\Exception $e) {
            $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
            Craft::error($e->getMessage() . ': thrown with API key: ' . $api, __METHOD__);
        }
    }
}
