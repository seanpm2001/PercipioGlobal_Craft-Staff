<?php

namespace percipiolondon\staff\jobs;

use Craft;
use craft\helpers\App;
use craft\helpers\Queue;
use craft\queue\BaseJob;
use percipiolondon\staff\elements\PayRun;
use percipiolondon\staff\helpers\Logger;
use percipiolondon\staff\records\Employer as EmployerRecord;
use percipiolondon\staff\records\PayRun as PayRunRecord;
use percipiolondon\staff\records\PayRunLog as PayRunLogRecord;
use percipiolondon\staff\Staff;

class CreatePayRunByEmployerJob extends BaseJob
{
    public $criteria;

    public function execute($queue): void
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

        if ($this->criteria['employer']) {
            $id = $this->criteria['employer']['staffologyId'] ?? '';
            $taxYear = $this->criteria['taxYear'] === '' ? $this->criteria['employer']['currentYear'] : $this->criteria['taxYear'];
            $payPeriod = $this->criteria['employer']['defaultPayOptions']['period'] ?? 'Monthly';

            $url = '/employers/' . $id . '/payrun/' . $taxYear . '/' . $payPeriod;

            try {
                $response = $client->get(Staff::$plugin->getSettings()->apiBaseUrl . $url, $headers);
                $payRunData = json_decode($response->getBody()->getContents(), true);

                if ($payRunData) {
                    $this->criteria['employer']['id'] = $this->criteria['employer']['staffologyId'];

                    Staff::$plugin->payRuns->fetchPayCodesList($this->criteria['employer']);
                    Staff::$plugin->payRuns->fetchPayRuns($payRunData, $this->criteria['employer']);
                }
            } catch (\Exception $e) {
                $logger->stdout(PHP_EOL, $logger::RESET);
                $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
                Craft::error($e->getMessage(), __METHOD__);
            }
        }
    }
}