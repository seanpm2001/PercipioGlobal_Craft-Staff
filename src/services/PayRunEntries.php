<?php

namespace percipiolondon\staff\services;

use Craft;
use craft\base\Component;
use craft\helpers\App;
use craft\helpers\Json;
use Exception;
use GuzzleHttp\Client;
use percipiolondon\staff\elements\PayRunEntry;
use percipiolondon\staff\helpers\Logger;
use percipiolondon\staff\helpers\Security as SecurityHelper;
use percipiolondon\staff\jobs\FetchPaySlipJob;
use percipiolondon\staff\records\Employee as EmployeeRecord;
use percipiolondon\staff\records\Employer as EmployerRecord;
use percipiolondon\staff\records\PayRunEntry as PayRunEntryRecord;
use percipiolondon\staff\Staff;

class PayRunEntries extends Component
{
    /* GETTERS AND SETTERS */
    public function setPayRunEntry(array $entries): array
    {
        $savedEntries = [];

        foreach ($entries as $entry) {
            $payRunEntry = PayRunEntryRecord::findOne($entry['id'] ?? null);

            if ($payRunEntry) {
                $employer = EmployerRecord::findOne($payRunEntry['employerId'] ?? null);

                if ($employer) {
                    $employer = $employer->toArray();

                    $id = $payRunEntry['staffologyId'] ?? null;
                    $employerId = $employer['staffologyId'] ?? null;
                    $taxYear = $payRunEntry['taxYear'] ?? null;
                    $payPeriod = $payRunEntry['payPeriod'] ?? 'Monthly';
                    $period = $payRunEntry['period'] ?? null;

                    $api = App::parseEnv(Staff::$plugin->getSettings()->apiKeyStaffology);
                    $base_url = 'https://api.staffology.co.uk/employers/' . $employerId . '/payrun/' . $taxYear . '/' . $payPeriod . '/' . $period . '/' . $id;
                    $credentials = base64_encode('staff:' . $api);
                    $headers = [
                        'headers' => [
                            'Authorization' => 'Basic ' . $credentials,
                        ],
                    ];

                    $client = new Client();

                    try {
                        $response = $client->get($base_url, $headers);
                        $payRunEntryData = Json::decodeIfJson($response->getBody()->getContents(), true);

                        $payRunEntry = $this->savePayRunEntry($payRunEntryData, $employer, $payRunEntry['payRunId']);

                        if ($payRunEntry) {
                            $savedEntries[] = $payRunEntry;
                        }
                    } catch (Exception $e) {

                        Craft::error($e->getMessage(), __METHOD__);
                    }
                }
            }
        }

        return $savedEntries;
    }


    /* FETCHES */

    public function fetchPaySlip(array $payRunEntry, array $employer): void
    {
        $queue = Craft::$app->getQueue();
        $queue->push(new FetchPaySlipJob([
            'description' => 'Fetch Pay Slips',
            'criteria' => [
                'employer' => $employer,
                'payPeriod' => $payRunEntry['payPeriod'] ?? null,
                'periodNumber' => $payRunEntry['period'] ?? null,
                'taxYear' => $payRunEntry['taxYear'] ?? null,
                'payRunEntry' => $payRunEntry ?? null
            ]
        ]));
    }


    /* SAVES */
    public function savePayRunEntry(array $payRunEntryData, array $employer, int $payRunId): ?PayRunEntry
    {
        $logger = new Logger();
        $logger->stdout("✓ Save pay run entry for " . $payRunEntryData['employee']['name'] ?? '' . '...', $logger::RESET);

        $payRunEntryRecord = PayRunEntry::findOne(['staffologyId' => $payRunEntryData['id'] ?? null]);

        try {
            if (!$payRunEntryRecord) {
                $payRunEntryRecord = new PayRunEntry();
            }

            //foreign keys
            $totalsId = $payRunEntryRecord->totalsId ?? null;
            $totalsYtdId = $payRunEntryRecord->totalsYtdId ?? null;
            $payOptionsId = $payRunEntryRecord->payOptionsId ?? null;
            $pensionSummaryId = $payRunEntryRecord->pensionSummaryId ?? null;

            $totals = Staff::$plugin->totals->saveTotals($payRunEntryData['totals'], $totalsId);
            $totalsYtd = Staff::$plugin->totals->saveTotals($payRunEntryData['totalsYtd'], $totalsYtdId);
            $payOptions = Staff::$plugin->payOptions->savePayOptions($payRunEntryData['payOptions'], $payOptionsId);
            $pensionSummary = $payRunEntryData['pensionSummary'] ?? null ? $this->savePensionSummary($payRunEntryData['pensionSummary'], $pensionSummaryId) : null;
            $employee = EmployeeRecord::findOne(['staffologyId' => $payRunEntryData['employee']['id'] ?? null]);

            $employerRecord = is_int($employer['id'] ?? null) ? $employer : EmployerRecord::findOne(['staffologyId' => $employer['id'] ?? null]);

            //save
            $payRunEntryRecord->employerId = $employerRecord['id'] ?? null;
            $payRunEntryRecord->employeeId = $employee->id ?? null;
            $payRunEntryRecord->payRunId = $payRunId ?? null;
            $payRunEntryRecord->payOptionsId = $payOptions->id ?? null;
            $payRunEntryRecord->totalsId = $totals->id ?? null;
            $payRunEntryRecord->totalsYtdId = $totalsYtd->id ?? null;
            $payRunEntryRecord->pensionSummaryId = $pensionSummary->id ?? null;
            $payRunEntryRecord->staffologyId = $payRunEntryData['id'] ?? null;
            $payRunEntryRecord->taxYear = $payRunEntryData['taxYear'] ?? null;
            $payRunEntryRecord->startDate = $payRunEntryData['startDate'] ?? null;
            $payRunEntryRecord->endDate = $payRunEntryData['endDate'] ?? null;
            $payRunEntryRecord->note = $payRunEntryData['note'] ?? null;
            $payRunEntryRecord->bacsSubReference = $payRunEntryData['bacsSubReference'] ?? null;
            $payRunEntryRecord->bacsHashcode = $payRunEntryData['bacsHashcode'] ?? null;
            $payRunEntryRecord->percentageOfWorkingDaysPaidAsNormal = $payRunEntryData['percentageOfWorkingDaysPaidAsNormal'] ?? null;
            $payRunEntryRecord->workingDaysNotPaidAsNormal = $payRunEntryData['workingDaysNotPaidAsNormal'] ?? null;
            $payRunEntryRecord->payPeriod = $payRunEntryData['payPeriod'] ?? null;
            $payRunEntryRecord->ordinal = $payRunEntryData['ordinal'] ?? null;
            $payRunEntryRecord->period = $payRunEntryData['period'] ?? null;
            $payRunEntryRecord->isNewStarter = $payRunEntryData['isNewStarter'] ?? null;
            $payRunEntryRecord->unpaidAbsence = $payRunEntryData['unpaidAbsence'] ?? null;
            $payRunEntryRecord->hasAttachmentOrders = $payRunEntryData['hasAttachmentOrders'] ?? null;
            $payRunEntryRecord->paymentDate = $payRunEntryData['paymentDate'] ?? null;
            $payRunEntryRecord->forcedCisVatAmount = $payRunEntryData['forcedCisVatAmount'] ?? null;
            $payRunEntryRecord->holidayAccrued = $payRunEntryData['holidayAccrued'] ?? null;
            $payRunEntryRecord->state = $payRunEntryData['state'] ?? null;
            $payRunEntryRecord->isClosed = $payRunEntryData['isClosed'] ?? null;
            $payRunEntryRecord->manualNi = $payRunEntryData['manualNi'] ?? null;
            $payRunEntryRecord->payrollCodeChanged = $payRunEntryData['payrollCodeChanged'] ?? null;
            $payRunEntryRecord->aeNotEnroledWarning = $payRunEntryData['aeNotEnroledWarning'] ?? null;
            $payRunEntryRecord->receivingOffsetPay = $payRunEntryData['receivingOffsetPay'] ?? null;
            $payRunEntryRecord->paymentAfterLearning = $payRunEntryData['paymentAfterLearning'] ?? null;
            $payRunEntryRecord->pdf = '';

            $elementsService = Craft::$app->getElements();
            $success = $elementsService->saveElement($payRunEntryRecord);

            if ($success) {
                $logger->stdout(" done" . PHP_EOL, $logger::FG_GREEN);
            } else {
                $logger->stdout(" failed" . PHP_EOL, $logger::FG_RED);

                $errors = "";

                foreach ($payRunEntryRecord->errors as $err) {
                    $errors .= implode(',', $err);
                }

                $logger->stdout($errors . PHP_EOL, $logger::FG_RED);
                Craft::error($payRunEntryRecord->errors, __METHOD__);
            }

            return $payRunEntryRecord;
        } catch (Exception $e) {

            $logger = new Logger();
            $logger->stdout(PHP_EOL, $logger::RESET);
            $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
            Craft::error($e->getMessage(), __METHOD__);
        }

        return null;
    }

    public function savePaySlip(array $paySlip, array $payRunEntry): void
    {
        $logger = new Logger();
        $logger->stdout('✓ Save pay slip of ' . $payRunEntry['employee']['name'] ?? '' . '...', $logger::RESET);

        $record = PayRunEntryRecord::findOne(['staffologyId' => $payRunEntry['id']]);

        if ($paySlip['content'] && $record) {
            $record->pdf = SecurityHelper::encrypt($paySlip['content'] ?? '');

            $success = $record->save();

            if ($success) {
                $logger->stdout(' done' . PHP_EOL, $logger::FG_GREEN);
            } else {
                $logger->stdout(PHP_EOL, $logger::RESET);
                $logger->stdout("The payslip couldn't be created for " . $payRunEntry['employee']['name'] ?? '' . PHP_EOL, $logger::FG_RED);
                Craft::error("The payslip couldn't be created for " . $payRunEntry['employee']['name'] ?? '', __METHOD__);
            }
        }
    }


    /* UPDATES */
    public function updatePayRunEntry(string $payPeriod, int $employer, int $payRunId, array $payRunEntryUpdate): bool
    {
        $employer = EmployerRecord::findOne($employer);

        if ($employer) {

            $api = App::parseEnv(Staff::$plugin->getSettings()->apiKeyStaffology);
            $base_url = 'https://api.staffology.co.uk/employers/' . $employer['staffologyId'] . '/payrun/' . $payPeriod . '/importpay?linesOnly=true';
            $credentials = base64_encode('staff:' . $api);
            $client = new \GuzzleHttp\Client([
                'headers' => [
                    'Authorization' => 'Basic ' . $credentials,
                ],
            ]);


            # START TEST
//            var_dump($base_url);
//            echo "<br/><br/>";
//            var_dump(json_encode($payRunEntryUpdate));
//            echo "<br/>";
            return true;
//            Craft::dd((array)$payRunEntryUpdate);
            #END TEST

            try {
                $response = $client->post(
                    $base_url,
                    [
                        'json' => $payRunEntryUpdate,
                    ]
                );

                $this->fetchPayRunByPayRunId($payRunId, true);

                return true;
            } catch (GuzzleException $e) {

                Craft::error($e->getMessage(), __METHOD__);

                return false;
            }
        }

        return false;
    }
}