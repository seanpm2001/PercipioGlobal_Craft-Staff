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

                $url = Staff::$plugin->getSettings()->apiBaseUrl . 'employers/' . $id . '/payrun/' . $taxYear . '/' . $payPeriod;

                try {

                    $response = $client->get(Staff::$plugin->getSettings()->apiBaseUrl . $url, $headers);
                    $payRunData = Json::decode($response->getBody()->getContents(), true);

                    if ($payRunData) {
                        Staff::$plugin->payRuns->syncPayRuns($employer, $payRunData);

                        foreach ($payRunData as $i => $payRun) {
                            $url = Staff::$plugin->getSettings()->apiBaseUrl . strpos($payRun['url'], 'api.staffology') > 0 ? str_replace('https://api.staffology.co.uk', '', $payRun['url']) : $payRun['url'];

                            $taxYear = $payRun['metadata']['taxYear'] ?? $payRun['taxYear'] ?? '';
                            $name = $payRun['name'] ?? '';

                            $logger->stdout('[' . $i+1 . '/' . count($payRunData) .'] ↧ Fetching pay run info of ' . $taxYear . ' / ' . $name . '...', $logger::RESET);

                            $response = $client->get( $url, $headers);
                            $payRunData = Json::decode($response->getBody()->getContents(), true);

                            $logger->stdout(' done' . PHP_EOL, $logger::FG_GREEN);

                            Staff::$plugin->payRuns->savePayRun($payRunData, $url, $employer);

                            $this->setProgress(
                                $queue,
                                $i / count($payRunData),
                                \Craft::t('staff-management', 'Employers fetch: {step, number} of {total, number}', [
                                    'step' => $i + 1,
                                    'total' => count($payRunData),
                                ])
                            );

                        }

//                        Staff::$plugin->payRuns->fetchPayCodesList($this->criteria['employer']);
//                        Staff::$plugin->payRuns->fetchPayRuns($payRunData, $this->criteria['employer']);
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