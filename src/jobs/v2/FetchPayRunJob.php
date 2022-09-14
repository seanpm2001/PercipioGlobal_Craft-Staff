<?php

namespace percipiolondon\staff\jobs\v2;

use Craft;
use craft\helpers\App;
use craft\helpers\Json;
use craft\queue\BaseJob;
use percipiolondon\staff\helpers\Logger;
use percipiolondon\staff\Staff;

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

            foreach ($this->criteria['employers'] as $employer) {

                $id = $employer['staffologyId'] ?? '';
                $taxYear = $employer['currentYear'];
                $payPeriod = $employer['defaultPayOptions']['period'] ?? 'Monthly';

                // fetch pay code

                try {
                    $response = $client->get(Staff::$plugin->getSettings()->apiBaseUrl . 'employers/' . $employer->staffologyId . '/paycodes', $headers);
                    $payCodes = Json::decodeIfJson($response->getBody()->getContents(), true);

                    $logger->stdout(' done' . PHP_EOL, $logger::FG_GREEN);

                    foreach ($payCodes as $payCode) {

                        $logger->stdout('↧ Fetch pay code info from ' . $payCode['name'], $logger::RESET);

                        try {
                            $response = $client->get($payCode['url'], $headers);
                            $result = Json::decodeIfJson($response->getBody()->getContents(), true);

                            $logger->stdout(' done' . PHP_EOL, $logger::FG_GREEN);

                            $fetchedPayCodes[] = $result;
                        } catch (\Exception $e) {
                            $logger->stdout(PHP_EOL, $logger::RESET);
                            $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
                            Craft::error($e->getMessage(), __METHOD__);
                        }
                    }

                    //Delete existing if they don't exist on Staffology anymore
                    Staff::$plugin->payRuns->syncPayCode($employer, $fetchedPayCodes);

                    //Save pay codes
                    foreach($fetchedPayCodes as $payCode) {
                        Staff::$plugin->payRuns->savePayCode($payCode, $employer);
                    }

                } catch (\Exception $e) {
                    $logger->stdout(PHP_EOL, $logger::RESET);
                    $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
                    Craft::error($e->getMessage(), __METHOD__);
                }

                // fetch pay run
                $url = Staff::$plugin->getSettings()->apiBaseUrl . 'employers/' . $id . '/payrun/' . $taxYear . '/' . $payPeriod;
                try {

                    $response = $client->get($url, $headers);
                    $payRunData = Json::decode($response->getBody()->getContents(), true);

                    if ($payRunData) {
                        Staff::$plugin->payRuns->syncPayRuns($employer, $payRunData);

                        foreach ($payRunData as $i => $payRun) {

                            $url = Staff::$plugin->getSettings()->apiBaseUrl . (strpos($payRun['url'], 'api.staffology') > 0 ? str_replace('https://api.staffology.co.uk', '', $payRun['url']) : $payRun['url']);

                            $taxYear = $payRun['metadata']['taxYear'] ?? $payRun['taxYear'] ?? '';
                            $name = $payRun['name'] ?? '';
                            $logger->stdout('[' . $i+1 . '/' . count($payRunData) .'] ↧ Fetching pay run info of ' . $taxYear . ' / ' . $name . '...', $logger::FG_PURPLE);

                            $response = $client->get( $url, $headers);
                            $payRunFetchedData = Json::decode($response->getBody()->getContents(), true);

                            $logger->stdout(' done' . PHP_EOL, $logger::FG_GREEN);

                            $payRunElement = Staff::$plugin->payRuns->savePayRun($payRunFetchedData, $url, $employer->toArray(), false);

                            // pay run entries
                            if ($payRunElement) {
                                foreach ($payRunFetchedData['entries'] as $j => $payRunEntryData) {
                                    $logger->stdout('Pay run ' . $taxYear . ' / ' . $name . '[' . $i+1 . '/' . count($payRunData) .']' . ' - Pay run entry [' . $j+1 . '/' . count($payRunFetchedData['entries']) . '] ↧ Fetching pay run entry of ' . $payRunEntryData['name'] . '...', $logger::RESET);
                                    $logger->stdout(' done' . PHP_EOL, $logger::FG_GREEN);

                                    $url = Staff::$plugin->getSettings()->apiBaseUrl . $payRunEntryData['url'];

                                    try {
                                        $response = $client->get($url, $headers);
                                        $result = Json::decode($response->getBody()->getContents(), true);

                                        Staff::$plugin->payRunEntries->savePayRunEntry($result, $employer->toArray(), $payRunElement->id);

                                        if(!App::parseEnv('$HUB_DEV_MODE')) {
                                            Staff::$plugin->payRunEntries->fetchPaySlip($result, $this->criteria['employer']);
                                        }
                                    } catch (\Exception $e) {
                                        $logger->stdout(PHP_EOL, $logger::RESET);
                                        $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
                                        Craft::error($e->getMessage(), __METHOD__);
                                    }
                                }
                            }

                        }
                    }
                } catch (\Exception $e) {
                    $logger->stdout(PHP_EOL, $logger::RESET);
                    $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
                    Craft::error($e->getMessage(), __METHOD__);
                }
            }

        } catch (\Exception $e) {
            $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
            Craft::error($e->getMessage(), __METHOD__);
        }
    }
}