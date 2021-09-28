<?php

namespace percipiolondon\craftstaff\jobs;

use Craft;
use craft\helpers\Json;
use craft\queue\BaseJob;
use percipiolondon\craftstaff\records\Employer as EmployerRecord;
use percipiolondon\craftstaff\records\PayRunEntry as PayRunEntryRecord;
use percipiolondon\craftstaff\elements\PayRunEntry;
use yii\db\Exception;

class FetchPaySlip extends Basejob
{
    public $headers;
    public $payPeriod;
    public $periodNumber;
    public $taxYear;
    public $employerId;
    public $payRunEntry;

    public function execute($queue): void
    {
        $client = new \GuzzleHttp\Client();

        try {
            $employer = EmployerRecord::findOne(['id' =>$this->employerId]);
            $empoyerId = $employer->staffologyId ?? null;

            $base_url = "https://api.staffology.co.uk/employers/{$empoyerId}/reports/{$this->taxYear}/{$this->payPeriod}/{$this->periodNumber}/{$this->payRunEntry->staffologyId}/payslip";
            $response = $client->get($base_url, $this->headers);

            $payslip = $response->getBody()->getContents();

            if( $payslip ) {
                $payslip = Json::decodeIfJson($payslip, false);

                if($payslip->content) {
                   $this->payRunEntry->pdf = $payslip->content ?? null;

                   $success = $this->payRunEntry->save(false);

                    if(!$success){
                        throw new Exception("The payslip couldn't be created");
                    }
                }
            }


//            $payslips = $response->getBody()->getContents();
//
//            if( $payslips ) {
//                $payslips = Json::decodeIfJson($payslips, false);
//
//                Craft::warning("SUCCESSS: ".$payslips);
//            }

        } catch (\Exception $e) {
            Craft::error("Something went wrong: {$e->getMessage()}", __METHOD__);
        } catch (\Throwable $e) {
            Craft::error("Something went wrong: {$e->getMessage()}", __METHOD__);
        }
    }

    private function _savePaySlipToPayRunEntry($data)
    {
    }
}
