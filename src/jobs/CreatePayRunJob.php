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

class CreatePayRunJob extends BaseJob
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


        // FETCH DETAILED EMPLOYEE
        try {
            $current = 0;
            $total = count($this->criteria['payRuns']);

            //Delete existing if they don't exist on Staffology anymore
            Staff::$plugin->payRuns->syncPayRuns($this->criteria['employer'], $this->criteria['payRuns']);

            foreach ($this->criteria['payRuns'] as $payRun) {
                $current++;
                $progress = "[" . $current . "/" . $total . "] ";

                $employer = is_int($this->criteria['employer']['id'] ?? null) ? EmployerRecord::findOne($this->criteria['employer']['id'])->toArray() : $this->criteria['employer'];

                $url = strpos($payRun['url'], 'api.staffology') > 0 ? str_replace("https://api.staffology.co.uk", "", $payRun['url']) : $payRun['url'];
//
//                $payRunLog = PayRunLogRecord::findOne(['url' => $url]);
//
//                // SET PAYRUN IF IT HASN'T ALREADY BEEN FETCHED IN PAYRUNLOG
//                if(!$payRunLog) {

                $taxYear = $payRun['metadata']['taxYear'] ?? $payRun['taxYear'] ?? '';
                $name = $payRun['name'] ?? '';

                $logger->stdout($progress . "↧ Fetching pay run info of " . $taxYear . ' / ' . $name . '...', $logger::RESET);

                $response = $client->get("https://api.staffology.co.uk" . $url, $headers);
                $payRunData = json_decode($response->getBody()->getContents(), true);

                $logger->stdout(" done" . PHP_EOL, $logger::FG_GREEN);

                Staff::$plugin->payRuns->savePayRun($payRunData, $url, $employer);
//                }
            }
        } catch (\Exception $e) {
            $logger->stdout(PHP_EOL, $logger::RESET);
            $logger->stdout($e->getMessage() . ' - ' . __METHOD__ . PHP_EOL, $logger::FG_RED);
            Craft::error($e->getMessage(), __METHOD__);
        }
    }

//    private function _savePayRunLog($payRun, $url, $payRunId)
//    {
//        $payRunLog = new PayRunLogRecord();
//
//        $payRunLog->siteId = Craft::$app->getSites()->currentSite->id;
//        $payRunLog->taxYear = $payRun['taxYear'] ?? '';
//        $payRunLog->employeeCount = $payRun['employeeCount'] ?? null;
//        $payRunLog->lastPeriodNumber = $payRun['employeeCount'] ?? null;
//        $payRunLog->url = $url ?? '';
//        $payRunLog->employerId = $this->employerId;
//        $payRunLog->payRunId = $payRunId;
//
//        $payRunLog->save(true);
//    }
//
//    private function _savePayRun($payRun, $url)
//    {
//        $payRunRecord = PayRunRecord::findOne(['url' => $url]);
//
//        // CREATE PAYRUN IF NOT EXISTS
//        if(!$payRunRecord) {
//            $payRunRecord = new PayRun();
//
//            $payRunRecord->siteId = Craft::$app->getSites()->currentSite->id;
//            $payRunRecord->staffologyId = "";
//            $payRunRecord->employerId = $this->employerId;
//            $payRunRecord->taxYear = $payRun['taxYear'] ?? '';
//            $payRunRecord->taxMonth = $payRun['taxMonth'] ?? null;
//            $payRunRecord->payPeriod = $payRun['payPeriod'] ?? '';
//            $payRunRecord->ordinal = $payRun['ordinal'] ?? null;
//            $payRunRecord->period = $payRun['period'] ?? null;
//            $payRunRecord->startDate = $payRun['startDate'] ?? null;
//            $payRunRecord->endDate = $payRun['endDate'] ?? null;
//            $payRunRecord->paymentDate = $payRun['paymentDate'] ?? null;
//            $payRunRecord->employeeCount = $payRun['employeeCount'] ?? null;
//            $payRunRecord->subContractorCount = $payRun['subContractorCount'] ?? null;
//            $payRunRecord->totals = $payRun['totals'] ?? '';
//            $payRunRecord->state = $payRun['state'] ?? '';
//            $payRunRecord->isClosed = $payRun['isClosed'] ?? '';
//            $payRunRecord->dateClosed = $payRun['dateClosed'] ?? null;
//            $payRunRecord->url = $url ?? '';
//
//            $elementsService = Craft::$app->getElements();
//            $success = $elementsService->saveElement($payRunRecord);
//
//            if($success) {
//                Craft::info("Saving pay run entries and log");
//
//                $this->_savePayRunLog($payRun, $url, $payRunRecord->id);
//
//                // GET PAYRUNENTRY FROM PAYRUN
//                Queue::push(new CreatePayRunEntryJob([
//                    'headers' => $this->headers,
//                    'payRunEntries' => $payRun['entries'],
//                    'payRunId' => $payRunRecord->id,
//                    'employerId' => $this->employerId,
//                ]));
//            }else{
//                Craft::error($payRunRecord->errors);
//            }
//        }
//    }
}
