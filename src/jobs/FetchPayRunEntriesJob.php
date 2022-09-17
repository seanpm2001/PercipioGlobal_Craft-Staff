<?php

namespace percipiolondon\staff\jobs;

use Craft;
use craft\helpers\App;
use craft\helpers\Json;
use craft\queue\BaseJob;
use percipiolondon\staff\elements\Employer;
use percipiolondon\staff\elements\PayRunEntry;
use percipiolondon\staff\helpers\Logger;
use percipiolondon\staff\helpers\Security;
use percipiolondon\staff\records\PayRunEntry as PayRunEntryRecord;
use percipiolondon\staff\Staff;

class FetchPayRunEntriesJob extends BaseJob
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
            foreach ($this->criteria['payRuns'] as $i => $payRun) {
                $employer = Employer::findOne($payRun->employerId);

                $logger->stdout('--- Start pay run entries fetching for ' . ($employer->name ?? '-') . ' [' . $payRun->taxYear . '/'  .$payRun->taxMonth . ']' . PHP_EOL, $logger::RESET);

                try {
                    $response = $client->get($payRun['url'], $headers);
                    $result = Json::decode($response->getBody()->getContents(), true);

                    foreach ($result['entries'] as $j => $payRunEntryData) {

                        $url = Staff::$plugin->getSettings()->apiBaseUrl . $payRunEntryData['url'];
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
                                    $payRunEntry = PayRunEntryRecord::findOne($payRunEntry->id);
                                    Staff::$plugin->payRunEntries->savePaySlip($paySlip, $payRunEntry);
                                }
                            } catch (\Exception $e) {
                                $logger->stdout(PHP_EOL, $logger::RESET);
                                $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
                                Craft::error($e->getMessage(), __METHOD__);
                            }
                        }

                    }
                } catch (\Exception $e) {
                    $logger->stdout(PHP_EOL, $logger::RESET);
                    $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
                    Craft::error($e->getMessage() . ': thrown with API key: ' . $api, __METHOD__);
                }

                $this->setProgress(
                    $queue,
                    $i / count($this->criteria['payRuns']),
                    Craft::t('staff-management', 'Pay run entries fetch from ' . ($employer->name ?? '-') . ' [' . $payRun->taxYear . '/'  .$payRun->taxMonth . ']: {step, number} of {total, number}', [
                        'step' => $i + 1,
                        'total' => count($this->criteria['payRuns']),
                    ])
                );

                $logger->stdout('--- End pay run entries fetching for ' . ($employer->name ?? '-') . ' [' . $payRun->taxYear . '/'  .$payRun->taxMonth . ']' . PHP_EOL, $logger::RESET);

            }
        } catch (\Exception $e) {
            $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
            Craft::error($e->getMessage() . ': thrown with API key: ' . $api, __METHOD__);
        }
    }
}