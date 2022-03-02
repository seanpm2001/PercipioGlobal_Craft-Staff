<?php

namespace percipiolondon\staff\jobs;

use Craft;
use craft\helpers\App;
use craft\queue\BaseJob;
use percipiolondon\staff\helpers\Logger;
use percipiolondon\staff\records\Employee;
use percipiolondon\staff\records\PayRunEntry as PayRunEntryRecord;
use percipiolondon\staff\elements\PayRunEntry;
use craft\helpers\Queue;
use percipiolondon\staff\Staff;

class CreatePayRunEntryJob extends Basejob
{
    public $criteria;

    public function execute($queue): void
    {
        $logger = new Logger();

        $api = App::parseEnv(Staff::$plugin->getSettings()->staffologyApiKey);
        $credentials = base64_encode('staff:'.$api);
        $headers = [
            'headers' => [
                'Authorization' => 'Basic ' . $credentials,
            ],
        ];
        $client = new \GuzzleHttp\Client();

        try {

            $current = 0;
            $total = count($this->criteria['payRunEntries']);

            foreach($this->criteria['payRunEntries'] as $payRunEntryData) {

                $current++;
                $progress = "[".$current."/".$total."] ";

                $logger->stdout($progress."↧ Fetching pay run entry of " . $payRunEntryData['taxYear'] . ' / ' . $payRunEntryData['taxYear'] . '...', $logger::RESET);

                $payRunEntry = PayRunEntryRecord::findOne(['staffologyId' => $payRunEntryData['id']]);

                // SET PAY RUN ENTRY IF IT DOESN'T EXIST
                if (!$payRunEntry) {

                    $logger->stdout(" done" . PHP_EOL, $logger::FG_GREEN);

                    $base_url = "https://api.staffology.co.uk/" . $payRunEntryData['url'];
                    $response = $client->get($base_url, $this->headers);
                    $payRunEntryData = json_decode($response->getBody()->getContents(), true);

                    Staff::getInstance()->payRun->savePayRunEntry($payRunEntryData, $this->criteria['employer']);
                    Staff::getInstance()->payRun->fetchPaySlip($payRunEntryData, $this->criteria['employer']);
//                    $this->_savePayRunEntry($payRunEntryData);
                }
            }
        } catch (\Exception $e) {

            $logger->stdout(PHP_EOL, $logger::RESET);
            $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
            Craft::error($e->getMessage(), __METHOD__);

        }
    }

//    private function _savePayRunEntry($payRunEntryData)
//    {
//        $payRunEntry = new PayRunEntry();
//
//        $employee = Employee::findOne(['staffologyId' => $payRunEntryData['employee']['id']]);
//
//        $payRunEntry->siteId = Craft::$app->getSites()->currentSite->id;
//        $payRunEntry->employerId = $this->employerId;
//        $payRunEntry->employeeId = $employee->id ?? null;
//        $payRunEntry->payRunId = $this->payRunId;
//        $payRunEntry->staffologyId = $payRunEntryData['id'] ?? null;
//        $payRunEntry->taxYear = $payRunEntryData['taxYear'] ?? null;
//        $payRunEntry->startDate = $payRunEntryData['startDate'] ?? null;
//        $payRunEntry->endDate = $payRunEntryData['endDate'] ?? null;
//        $payRunEntry->note = $payRunEntryData['note'] ?? '';
//        $payRunEntry->bacsSubReference = $payRunEntryData['bacsSubReference'] ?? '';
//        $payRunEntry->bacsHashcode = $payRunEntryData['bacsHashcode'] ?? '';
//        $payRunEntry->percentageOfWorkingDaysPaidAsNormal = $payRunEntryData['percentageOfWorkingDaysPaidAsNormal'] ?? null;
//        $payRunEntry->workingDaysNotPaidAsNormal = $payRunEntryData['workingDaysNotPaidAsNormal'] ?? null;
//        $payRunEntry->payPeriod = $payRunEntryData['payPeriod'] ?? null;
//        $payRunEntry->ordinal = $payRunEntryData['ordinal'] ?? null;
//        $payRunEntry->period = $payRunEntryData['period'] ?? null;
//        $payRunEntry->isNewStarter = $payRunEntryData['isNewStarter'] ?? null;
//        $payRunEntry->unpaidAbsence = $payRunEntryData['unpaidAbsence'] ?? null;
//        $payRunEntry->hasAttachmentOrders = $payRunEntryData['hasAttachmentOrders'];
//        $payRunEntry->paymentDate = $payRunEntryData['paymentDate'] ?? null;
//        $payRunEntry->priorPayrollCode = $payRunEntryData['priorPayrollCode'] ?? '';
//        $payRunEntry->payOptions = $payRunEntryData['payOptions'] ?? '';
//        $payRunEntry->pensionSummary = $payRunEntryData['pensionSummary'] ?? '';
//        $payRunEntry->totals = $payRunEntryData['totals'] ?? '';
//        $payRunEntry->periodOverrides = $payRunEntryData['periodOverrides'] ?? '';
//        $payRunEntry->totalsYtd = $payRunEntryData['totalsYtd'] ?? '';
//        $payRunEntry->totalsYtdOverrides = $payRunEntryData['totalsYtdOverrides'] ?? '';
//        $payRunEntry->forcedCisVatAmount = $payRunEntryData['forcedCisVatAmount'] ?? null;
//        $payRunEntry->holidayAccured = $payRunEntryData['holidayAccured'] ?? null;
//        $payRunEntry->state = $payRunEntryData['state'] ?? '';
//        $payRunEntry->isClosed = $payRunEntryData['isClosed'] ?? null;
//        $payRunEntry->manualNi = $payRunEntryData['manualNi'] ?? null;
//        $payRunEntry->nationalInsuranceCalculation = $payRunEntryData['nationalInsuranceCalculation'] ?? '';
//        $payRunEntry->aeNotEnroledWarning = $payRunEntryData['aeNotEnroledWarning'] ?? null;
//        $payRunEntry->fps = $payRunEntryData['fps'] ?? '';
//        $payRunEntry->receivingOffsetPay = $payRunEntryData['receivingOffsetPay'] ?? null;
//        $payRunEntry->paymentAfterLearning = $payRunEntryData['paymentAfterLearning'] ?? null;
//        $payRunEntry->umbrellaPayment = $payRunEntryData['umbrellaPayment'] ?? '';
//        $payRunEntry->employee = $payRunEntryData['employee'] ?? '';
////        $payRunEntry->pdf = $payRunEntryData['pdf'];
//
//        $elementsService = Craft::$app->getElements();
//        $success = $elementsService->saveElement($payRunEntry);
//
//        if(!$success){
//            Craft::error($payRunEntry->errors);
//        }
//    }

    protected function defaultDescription(): string
    {
        return sprintf(
            'Fetching Pay Run Entries from "%s"',
            $this->criteria['employer']['id']
        );
    }
}
