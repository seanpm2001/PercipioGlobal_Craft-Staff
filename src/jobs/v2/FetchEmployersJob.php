<?php

namespace percipiolondon\staff\jobs\v2;

use Craft;
use craft\helpers\App;
use craft\helpers\Json;
use craft\queue\BaseJob;
use percipiolondon\staff\helpers\Logger;
use percipiolondon\staff\Staff;

class FetchEmployersJob extends BaseJob
{
    public array $criteria = [];

    public function execute($queue)
    {
        $logger = new Logger();
        $api = App::parseEnv(Staff::$plugin->getSettings()->apiKeyStaffology);
        $credentials = base64_encode('staff:' . $api);
        $base_url = Staff::$plugin->getSettings()->apiBaseUrl . 'employers';
        $headers = [
            'headers' => [
                'Authorization' => 'Basic ' . $credentials,
            ],
        ];
        $client = new \GuzzleHttp\Client();

        try {
            $response = $client->get($base_url, $headers);

            $employers = Json::decodeIfJson($response->getBody()->getContents(), true);

            //Delete existing if they don't exist on Staffology anymore
//            Staff::$plugin->employers->syncEmployers($employers);

            foreach ($employers as $i => $employer) {

                try {
                    $logger->stdout('[' . $i . '/' . count($employers) . '] ↧ Fetch employer info from ' . $employer['name'] . ' (' . $employer['id'] . ')', $logger::RESET);
                    $logger->stdout(' done' . PHP_EOL, $logger::FG_GREEN);

                    $response = $client->get($employer['url'], $headers);
                    $result = Json::decodeIfJson($response->getBody()->getContents(), true);

                    Staff::$plugin->employers->saveEmployer($result);

                } catch (\Exception $e) {
                    $logger->stdout(PHP_EOL, $logger::RESET);
                    $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
                    Craft::error($e->getMessage(), __METHOD__);
                }

                $this->setProgress(
                    $queue,
                    $i / count($employers),
                    \Craft::t('staff-management', 'Employers fetch: {step, number} of {total, number}', [
                        'step' => $i + 1,
                        'total' => count($employers),
                    ])
                );
            }

        } catch (\Exception $e) {
            $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
            Craft::error($e->getMessage() . ': thrown with API key: ' . $api, __METHOD__);
        }
    }
}