<?php

namespace percipiolondon\staff\jobs;

use Craft;
use craft\helpers\App;
use craft\helpers\Json;
use craft\queue\BaseJob;
use percipiolondon\staff\helpers\Logger;
use percipiolondon\staff\records\Employer as EmployerRecord;
use percipiolondon\staff\records\PayRunEntry as PayRunEntryRecord;
use percipiolondon\staff\elements\PayRunEntry;
use percipiolondon\staff\Staff;
use yii\db\Exception;

class FetchPaySlipJob extends Basejob
{
    public $criteria;

    public function execute($queue): void
    {
        $logger = new Logger();

        // connection props
        $api = App::parseEnv(Staff::$plugin->getSettings()->staffologyApiKey);
        $base_url = 'https://api.staffology.co.uk/';
        $credentials = base64_encode('staff:'.$api);
        $headers = [
            'headers' => [
                'Authorization' => 'Basic ' . $credentials,
            ],
        ];
        $client = new \GuzzleHttp\Client();

        try {
            $employer = EmployerRecord::findOne(['id' => $this->criteria['employer']['id']]);
            $empoyerId = $this->criteria['employer']['id'] ?? null;

            $base_url = "https://api.staffology.co.uk/employers/{$empoyerId}/reports/{$this->criteria['taxYear']}/{$this->criteria['payPeriod']}/{$this->criteria['periodNumber']}/{$this->criteria['payRunEntry']['staffologyId']}/payslip";
            $response = $client->get($base_url, $headers);

            $payslip = $response->getBody()->getContents();

            if( $payslip ) {
                $payslip = Json::decodeIfJson($payslip, $this->criteria['payRunEntry'], true);

                Staff::$plugin->payRun->savePaySlip($payslip, $this->criteria['employer']);
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
