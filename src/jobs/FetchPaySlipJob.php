<?php

namespace percipiolondon\staff\jobs;

use Craft;
use craft\helpers\App;
use craft\helpers\Json;
use craft\queue\BaseJob;
use percipiolondon\staff\helpers\Logger;
use percipiolondon\staff\Staff;

class FetchPaySlipJob extends Basejob
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
                'Accept' => 'application/pdf',
            ],
        ];
        $client = new \GuzzleHttp\Client();

        $logger->stdout('↧ Fetching pay slip...', $logger::RESET);

        try {
            $base_url = "https://api.staffology.co.uk/employers/{$this->criteria['employer']['id']}/reports/{$this->criteria['payRunEntry']['taxYear']}/{$this->criteria['payRunEntry']['payPeriod']}/{$this->criteria['payRunEntry']['period']}/{$this->criteria['payRunEntry']['id']}/payslip";
            // /employers/{employerId}/reports/{taxYear}/{payPeriod}/{periodNumber}/{id}/payslip
            $response = $client->get($base_url, $headers);

            $paySlip = $response->getBody()->getContents();

            $logger->stdout(" done" . PHP_EOL, $logger::FG_GREEN);

            if ($paySlip) {
                $paySlip = Json::decodeIfJson($paySlip, true);

                Staff::$plugin->payRuns->savePaySlip($paySlip, $this->criteria['payRunEntry']);
            }

//            $payslips = $response->getBody()->getContents();
//
//            if( $payslips ) {
//                $payslips = Json::decodeIfJson($payslips, false);
//
//                Craft::warning("SUCCESSS: ".$payslips);
//            }
        } catch (\Exception $e) {
            $logger->stdout(PHP_EOL, $logger::RESET);
            $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
            Craft::error($e->getMessage(), __METHOD__);
        }
    }

    private function _savePaySlipToPayRunEntry($data)
    {
    }
}
