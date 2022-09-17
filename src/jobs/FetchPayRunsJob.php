<?php

namespace percipiolondon\staff\jobs;

use Craft;
use craft\helpers\App;
use craft\helpers\Json;
use craft\queue\BaseJob;
use percipiolondon\staff\helpers\Logger;
use percipiolondon\staff\Staff;
use percipiolondon\staff\records\PayRunEntry as PayRunEntryRecord;

class FetchPayRunsJob extends BaseJob
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

                $logger->stdout('--- Start pay run fetching for ' . $employer->name . PHP_EOL, $logger::RESET);

                $id = $employer['staffologyId'] ?? '';
                $taxYear = $this->criteria['taxYear'] ?? $employer['currentYear'];
                $payPeriod = $employer['defaultPayOptions']['period'] ?? 'Monthly';

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
                $url = Staff::$plugin->getSettings()->apiBaseUrl . 'employers/' . $id . '/payrun/' . $taxYear . '/' . $payPeriod;
                try {

                    $response = $client->get($url, $headers);
                    $payRunData = Json::decode($response->getBody()->getContents(), true);

                    if ($payRunData) {
                        Staff::$plugin->payRuns->syncPayRuns($employer, $payRunData);

                        foreach ($payRunData as $i => $payRun) {

                            $url = Staff::$plugin->getSettings()->apiBaseUrl . (strpos($payRun['url'], 'api.staffology') > 0 ? str_replace('https://api.staffology.co.uk/', '', $payRun['url']) : $payRun['url']);

                            $name = $payRun['name'] ?? '';

                            $response = $client->get( $url, $headers);
                            $payRunFetchedData = Json::decode($response->getBody()->getContents(), true);


                            $payRunRecord = Staff::$plugin->payRuns->savePayRun($payRunFetchedData, $url, $employer->toArray());

                            $this->setProgress(
                                $queue,
                                $i / count($payRunData),
                                Craft::t('staff-management', 'Pay run fetch from ' . $employer->name . ': {step, number} of {total, number}', [
                                    'step' => $i + 1,
                                    'total' => count($payRunData),
                                ])
                            );

                            // pay run entries
                            if (($this->criteria['fetchEntries'] ?? false) && $payRunRecord) {

                                $logger->stdout('--- Start pay run entries fetching for ' . ($employer->name ?? '-') . ' [' . $payRunRecord['taxYear'] . '/'  .$payRunRecord['taxMonth'] . ']' . PHP_EOL, $logger::RESET);

                                foreach ($payRunFetchedData['entries'] as $j => $payRunEntryData) {
                                    $logger->stdout('Pay run ' . $taxYear . ' / ' . $name . '[' . $i+1 . '/' . count($payRunData) .']' . ' - Pay run entry [' . $j+1 . '/' . count($payRunFetchedData['entries']) . '] ↧ Fetching pay run entry of ' . $payRunEntryData['name'] . '...', $logger::RESET);
                                    $logger->stdout(' done' . PHP_EOL, $logger::FG_GREEN);

                                    $url = Staff::$plugin->getSettings()->apiBaseUrl . $payRunEntryData['url'];

                                    try {
                                        $response = $client->get($url, $headers);
                                        $result = Json::decode($response->getBody()->getContents(), true);

                                        $payRunEntry = Staff::$plugin->payRunEntries->savePayRunEntry($result, $employer->toArray(), $payRunRecord->id);

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
                                                    $payRunEntry = PayRunEntryRecord::findOne($payRunEntry->id);
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

                            $logger->stdout('--- End pay run entries fetching for ' . ($employer->name ?? '-') . ' [' . $payRunRecord->taxYear . '/'  .$payRunRecord->taxMonth . ']' . PHP_EOL, $logger::RESET);
                        }
                    }
                } catch (\Exception $e) {
                    $logger->stdout(PHP_EOL, $logger::RESET);
                    $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
                    Craft::error($e->getMessage() . ': thrown with API key: ' . $api, __METHOD__);
                }

                $logger->stdout('--- End pay run fetching for ' . $employer->name . PHP_EOL, $logger::RESET);
            }

        } catch (\Exception $e) {
            $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
            Craft::error($e->getMessage() . ': thrown with API key: ' . $api, __METHOD__);
        }
    }
}
