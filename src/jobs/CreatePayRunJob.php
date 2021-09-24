<?php

namespace percipiolondon\craftstaff\jobs;

use Craft;
use craft\helpers\Queue;
use craft\queue\BaseJob;
use percipiolondon\craftstaff\records\PayRun as PayRunRecord;
use percipiolondon\craftstaff\records\PayRunLog as PayRunLogRecord;
use percipiolondon\craftstaff\jobs\CreatePayRunEntryJob;

class CreatePayRunJob extends BaseJob
{
    public $paySchedules;
    public $headers;
    public $employerId;

    public function execute($queue): void
    {
        // FETCH DETAILED EMPLOYEE
        try {
            $client = new \GuzzleHttp\Client();

            foreach($this->paySchedules as $schedule) {

                if(count($schedule['payRuns']) > 0) {

                    foreach($schedule['payRuns'] as $payRun) {

                        $payRunLog = PayRunLogRecord::findOne(['url' => $payRun['url']]);

                        // SET PAYRUN IF IT HASN'T ALREADY BEEN FETCHED IN PAYRUNLOG
                        if(!$payRunLog) {

                            $response =  $client->get($payRun['url'], $this->headers);
                            $payRunData = json_decode($response->getBody()->getContents(), true);

                            $this->_savePayRun($payRunData, $payRun['url']);
                        }

                    }
                }

            }
        } catch (\Exception $e) {
            Craft::error("Something went wrong: {$e->getMessage()}", __METHOD__);
        } catch (\Throwable $e) {
            Craft::error("Something went wrong: {$e->getMessage()}", __METHOD__);
        }
    }

    private function _savePayRunLog($payRun, $url, $payRunId)
    {
        var_dump($payRunId);
        $payRunLog = new PayRunLogRecord();

        $payRunLog->siteId = Craft::$app->getSites()->currentSite->id;
        $payRunLog->taxYear = $payRun['taxYear'] ?? '';
        $payRunLog->employeeCount = $payRun['employeeCount'] ?? null;
        $payRunLog->lastPeriodNumber = $payRun['employeeCount'] ?? null;
        $payRunLog->url = $url ?? '';
        $payRunLog->employerId = $this->employerId;
        $payRunLog->payRunId = $payRunId;

        $payRunLog->save(true);
    }

    private function _savePayRun($payRun, $url)
    {
        $payRunRecord = PayRunRecord::findOne(['url' => $url]);

        // CREATE PAYRUN IF NOT EXISTS
        if(!$payRunRecord) {
            $payRunRecord = new PayRunRecord();

            $payRunRecord->siteId = Craft::$app->getSites()->currentSite->id;
            $payRunRecord->staffologyId = "";
            $payRunRecord->employerId = $this->employerId;
            $payRunRecord->taxYear = $payRun['taxYear'] ?? '';
            $payRunRecord->taxMonth = $payRun['taxMonth'] ?? null;
            $payRunRecord->payPeriod = $payRun['payPeriod'] ?? '';
            $payRunRecord->ordinal = $payRun['ordinal'] ?? null;
            $payRunRecord->period = $payRun['period'] ?? null;
            $payRunRecord->startDate = $payRun['startDate'] ?? null;
            $payRunRecord->endDate = $payRun['endDate'] ?? null;
            $payRunRecord->employeeCount = $payRun['employeeCount'] ?? null;
            $payRunRecord->subContractorCount = $payRun['subContractorCount'] ?? null;
            $payRunRecord->totals = $payRun['totals'] ?? '';
            $payRunRecord->state = $payRun['state'] ?? '';
            $payRunRecord->isClosed = $payRun['isClosed'] ?? '';
            $payRunRecord->dateClosed = $payRun['dateClosed'] ?? null;
            $payRunRecord->url = $url ?? '';

            $success = $payRunRecord->save(true);

            if($success) {
                Craft::info("Saving pay run entries and log");

                $this->_savePayRunLog($payRun, $url, $payRunRecord->id);

                // GET PAYRUNENTRY FROM PAYRUN
                Queue::push(new CreatePayRunEntryJob([
                    'headers' => $this->headers,
                    'payRunEntries' => $payRun['entries'],
                    'payRunId' => $payRunRecord->id,
                    'employerId' => $this->employerId,
                ]));
            }else{
                Craft::error($payRunRecord->errors);
            }
        }
    }
}
