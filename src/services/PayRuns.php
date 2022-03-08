<?php
/**
 * staff-management plugin for Craft CMS 3.x
 *
 * Craft Staff Management provides an HR solution for payroll and benefits
 *
 * @link      http://percipio.london
 * @copyright Copyright (c) 2021 Percipio
 */

namespace percipiolondon\staff\services;

use percipiolondon\staff\elements\PayRun;
use percipiolondon\staff\helpers\Logger;
use percipiolondon\staff\helpers\Security as SecurityHelper;
use percipiolondon\staff\jobs\FetchPayCodesListJob;
use percipiolondon\staff\jobs\FetchPaySchedulesJob;
use percipiolondon\staff\jobs\FetchPaySlipJob;
use percipiolondon\staff\jobs\CreatePayCodeJob;
use percipiolondon\staff\jobs\CreatePayRunJob;
use percipiolondon\staff\jobs\CreatePayRunEntryJob;

use percipiolondon\staff\records\PayRun as PayRunRecord;

use Craft;
use craft\base\Component;
use percipiolondon\staff\records\PayRunTotals;
use yii\db\Exception;

/**
 * PayRun Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    Percipio
 * @package   Staff
 * @since     1.0.0-alpha.1
 */
class PayRuns extends Component
{
    // Public Methods
    // =========================================================================
    public function fetchPayCodesList(array $employers)
    {
        $queue = Craft::$app->getQueue();
        $queue->push(new FetchPayCodesListJob([
            'description' => 'Fetch pay codes',
            'criteria' => [
                'employers' => $employers
            ]
        ]));
    }

    public function fetchPayCodes(array $payCodes)
    {
        $queue = Craft::$app->getQueue();
        $queue->push(new CreatePayCodeJob([
            'description' => 'Save pay codes',
            'criteria' => [
                'payCodes' => $payCodes,
            ]
        ]));
    }

    public function savePayCode(array $payCode)
    {
        $logger = new Logger();
        $logger->stdout("✓ Save pay code " . $payCode['code'] . "...", $logger::RESET);
        $logger->stdout(" done" . PHP_EOL, $logger::FG_GREEN);
    }

    public function fetchPayRun(array $employer)
    {
        $payRuns = $employer['metadata']['payruns'] ?? [];

        $queue = Craft::$app->getQueue();
        $queue->push(new CreatePayRunJob([
            'description' => 'Fetch pay runs',
            'criteria' => [
                'payRuns' => $payRuns,
                'employer' => $employer,
            ]
        ]));
    }

    public function savePayRun(array $payRun, string $payRunUrl, array $employer): void
    {
        $logger = new Logger();

        $payRunRecord = PayRunRecord::findOne(['url' => $payRunUrl]);

        // CREATE PAYRUN IF NOT EXISTS
        if(!$payRunRecord) {
            $logger->stdout("✓ Save pay run of " .$employer['name'] . ' ' . $payRun['taxYear'] .  ' / ' . $payRun['taxMonth'] . '...', $logger::RESET);

            $payRunRecord = new PayRun();

            $payRunRecord->employerId = $employer['id'];
            $payRunRecord->taxYear = $payRun['taxYear'] ?? '';
            $payRunRecord->taxMonth = $payRun['taxMonth'] ?? null;
            $payRunRecord->payPeriod = $payRun['payPeriod'] ?? '';
            $payRunRecord->ordinal = $payRun['ordinal'] ?? null;
            $payRunRecord->period = $payRun['period'] ?? null;
            $payRunRecord->startDate = $payRun['startDate'] ?? null;
            $payRunRecord->endDate = $payRun['endDate'] ?? null;
            $payRunRecord->paymentDate = $payRun['paymentDate'] ?? null;
            $payRunRecord->employeeCount = $payRun['employeeCount'] ?? null;
            $payRunRecord->subContractorCount = $payRun['subContractorCount'] ?? null;
            $payRunRecord->totals = $payRun['totals'] ?? '';
            $payRunRecord->state = $payRun['state'] ?? '';
            $payRunRecord->isClosed = $payRun['isClosed'] ?? '';
            $payRunRecord->dateClosed = $payRun['dateClosed'] ?? null;
            $payRunRecord->url = $payRunUrl ?? '';

            $elementsService = Craft::$app->getElements();
            $success = $elementsService->saveElement($payRunRecord);

            if($success) {
                // GET PAYRUNENTRY FROM PAYRUN
                $queue = Craft::$app->getQueue();
                $queue->push(new CreatePayRunEntryJob([
                    'description' => 'Fetch pay run entry',
                    'criteria' => [
                        'payRunEntries' => $payRun['entries'],
                        'employer' => $employer,
                    ]
                ]));

                $logger->stdout(" done" . PHP_EOL, $logger::FG_GREEN);

                Craft::info("Saving pay run entries and log");
                $this->savePayRunLog($payRun, $payRunUrl, $payRunRecord->id);

            }else{
                $logger->stdout(" failed" . PHP_EOL, $logger::FG_RED);

                $errors = "";

                foreach($payRunRecord->errors as $err) {
                    $errors .= implode(',', $err);
                }

                $logger->stdout($errors . PHP_EOL, $logger::FG_RED);
                Craft::error($payRunRecord->errors, __METHOD__);
            }
        }
    }

    public function fetchPaySlip(array $payRunEntry, array $employer)
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

    public function savePaySlip(array $paySlip, array $payRunEntry, array $employer)
    {
        $logger = new Logger();

        $logger->stdout("✓ Save pay slip...", $logger::RESET);
        $logger->stdout(" done" . PHP_EOL, $logger::FG_GREEN);

//        if($payslip->content) {
//            $this->criteria['payRunEntry']['pdf'] = $paySlip['content'] ?? null;
//
//            $success = $payRunEntry->save(false);
//
//            if(!$success){
//                $logger->stdout(PHP_EOL, $logger::RESET);
//                $logger->stdout("The payslip couldn't be created" . PHP_EOL, $logger::FG_RED);
//                Craft::error("The payslip couldn't be created", __METHOD__);
//            }
//        }
    }

    public function savePayRunLog(array $payRun, string $url, string $payRunId)
    {
        $logger = new Logger();
        $logger->stdout('✓ Save pay run log ...', $logger::RESET);
        $logger->stdout(" done" . PHP_EOL, $logger::FG_GREEN);
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
    }

    public function savePayRunEntry(array $payRunEntryData, string $employee, array $employer)
    {
        $logger = new Logger();

        $logger->stdout("✓ Save pay run entry for " . $employee . '...', $logger::RESET);
        $logger->stdout(" done" . PHP_EOL, $logger::FG_GREEN);

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
    }
    
    public function saveTotals(array $totals, int $totalsId = null): PayRunTotals
    {
        if($totalsId) {
            $record = PayRunTotals::findOne($totalsId);

            if (!$record) {
                throw new Exception('Invalid pay run totals ID: ' . $totalsId);
            }

        }else{
            $record = new PayRunTotals();
        }

        $record->basicPay = SecurityHelper::encrypt($totals['basicPay'] ?? '');
        $record->gross = SecurityHelper::encrypt($totals['gross'] ?? '');
        $record->grossForNi = SecurityHelper::encrypt($totals['grossForNi'] ?? '');
        $record->grossNotSubjectToEmployersNi = SecurityHelper::encrypt($totals['grossNotSubjectToEmployersNi'] ?? '');
        $record->grossForTax = SecurityHelper::encrypt($totals['grossForTax'] ?? '');
        $record->employerNi = SecurityHelper::encrypt($totals['employerNi'] ?? '');
        $record->employeeNi = SecurityHelper::encrypt($totals['employeeNi'] ?? '');
        $record->employerNiOffPayroll = $totals['employerNiOffPayroll'] ?? null;
        $record->realTimeClass1ANi = $totals['realTimeClass1ANi'] ?? null;
        $record->tax = SecurityHelper::encrypt($totals['tax'] ?? '');
        $record->netPay = SecurityHelper::encrypt($totals['netPay'] ?? '');
        $record->adjustments = SecurityHelper::encrypt($totals['adjustments'] ?? '');
        $record->additions = SecurityHelper::encrypt($totals['additions'] ?? '');
        $record->takeHomePay = SecurityHelper::encrypt($totals['takeHomePay'] ?? '');
        $record->nonTaxOrNICPmt = SecurityHelper::encrypt($totals['nonTaxOrNICPmt'] ?? '');
        $record->itemsSubjectToClass1NIC = $totals['itemsSubjectToClass1NIC'] ?? null;
        $record->dednsFromNetPay = $totals['dednsFromNetPay'] ?? null;
        $record->tcp_Tcls = $totals['tcp_Tcls'] ?? null;
        $record->tcp_Pp = $totals['tcp_Pp'] ?? null;
        $record->tcp_Op = $totals['tcp_Op'] ?? null;
        $record->flexiDd_Death = $totals['flexiDd_Death'] ?? null;
        $record->flexiDd_Death_NonTax = $totals['flexiDd_Death_NonTax'] ?? null;
        $record->flexiDd_Pension = $totals['flexiDd_Pension'] ?? null;
        $record->flexiDd_Pension_NonTax = $totals['flexiDd_Pension_NonTax'] ?? null;
        $record->smp = $totals['smp'] ?? null;
        $record->spp = $totals['spp'] ?? null;
        $record->sap = $totals['sap'] ?? null;
        $record->shpp = $totals['shpp'] ?? null;
        $record->spbp = $totals['spbp'] ?? null;
        $record->ssp = $totals['ssp'] ?? null;
        $record->studentLoanRecovered = SecurityHelper::encrypt($totals['studentLoanRecovered'] ?? '');
        $record->postgradLoanRecovered = SecurityHelper::encrypt($totals['postgradLoanRecovered'] ?? '');
        $record->pensionableEarnings = SecurityHelper::encrypt($totals['pensionableEarnings'] ?? '');
        $record->pensionablePay = SecurityHelper::encrypt($totals['pensionablePay'] ?? '');
        $record->nonTierablePay = SecurityHelper::encrypt($totals['nonTierablePay'] ?? '');
        $record->employeePensionContribution = SecurityHelper::encrypt($totals['employeePensionContribution'] ?? '');
        $record->employeePensionContributionAvc = SecurityHelper::encrypt($totals['employeePensionContributionAvc'] ?? '');
        $record->employerPensionContribution = SecurityHelper::encrypt($totals['employerPensionContribution'] ?? '');
        $record->empeePenContribnsNotPaid = SecurityHelper::encrypt($totals['empeePenContribnsNotPaid'] ?? '');
        $record->empeePenContribnsPaid = SecurityHelper::encrypt($totals['empeePenContribnsPaid'] ?? '');
        $record->attachmentOrderDeductions = SecurityHelper::encrypt($totals['attachmentOrderDeductions'] ?? '');
        $record->cisDeduction = SecurityHelper::encrypt($totals['cisDeduction'] ?? '');
        $record->cisVat = SecurityHelper::encrypt($totals['cisVat'] ?? '');
        $record->cisUmbrellaFee = SecurityHelper::encrypt($totals['cisUmbrellaFee'] ?? '');
        $record->cisUmbrellaFeePostTax = SecurityHelper::encrypt($totals['cisUmbrellaFeePostTax'] ?? '');
        $record->pbik = $totals['pbik'] ?? null;
        $record->mapsMiles = $totals['mapsMiles'] ?? null;
        $record->umbrellaFee = SecurityHelper::encrypt($totals['umbrellaFee'] ?? '');
        $record->appLevyDeduction = $totals['appLevyDeduction'] ?? null;
        $record->paymentAfterLeaving = $totals['paymentAfterLeaving'] ?? null;
        $record->taxOnPaymentAfterLeaving = $totals['taxOnPaymentAfterLeaving'] ?? null;
        $record->nilPaid = $totals['nilPaid'] ?? null;
        $record->leavers = $totals['leavers'] ?? null;
        $record->starters = $totals['starters'] ?? null;
        $record->totalCost = SecurityHelper::encrypt($totals['totalCost'] ?? '');

        $record->save();

        return $record;
    }


//    /**
//     * This function can literally be anything you want, and you can have as many service
//     * functions as you want
//     *
//     * From any other plugin file, call it like this:
//     *
//     *     Staff::$plugin->payRuns->exampleService()
//     *
//     * @return mixed
//     */
//    public function fetch()
//    {
//        $api = Craft::parseEnv(Staff::$plugin->getSettings()->apiKeyStaffology);
//        $credentials = base64_encode('staff:'.$api);
//        $headers = [
//            'headers' => [
//                'Authorization' => 'Basic ' . $credentials,
//            ],
//        ];
//        $client = new \GuzzleHttp\Client();
//
//        if ($api) {
//            // GET EMPLOYERS
//            $employers = Employer::find()->all();
//
//            foreach($employers as $employer) {
//                $base_url = "https://api.staffology.co.uk/employers/{$employer->staffologyId}/schedules/{$employer->currentYear}";
//
//                //GET LIST OF PAYSCHEDULES
//                try {
//
//                    $response = $client->get($base_url, $headers);
//                    $paySchedules = json_decode($response->getBody()->getContents(), true);
//
//                    Queue::push(new CreatePayRunJob([
//                        'headers' => $headers,
//                        'paySchedules' => $paySchedules,
//                        'employerId' => $employer->id,
//                    ]));
//
//                } catch (\Throwable $e) {
//                    echo "---- error -----\n";
//                    var_dump($e->getMessage());
//                    Craft::error($e->getMessage(), __METHOD__);
////                    Craft::dd($e);
//                    echo "\n---- end error ----";
//                }
//            }
//        }
//
//        return "success";
//    }
//
//    public function fetchPayslips()
//    {
//        $api = Craft::parseEnv(Staff::$plugin->getSettings()->apiKeyStaffology);
//        $credentials = base64_encode('staff:'.$api);
//        $headers = [
//            'headers' => [
//                'Authorization' => 'Basic ' . $credentials,
//                'Accept' => 'application/pdf'
//            ],
//        ];
//
//        if ($api) {
//            // GET EMPLOYERS
//            $payRunEntries = PayRunEntryRecord::find()->all();
//
//            foreach($payRunEntries as $payRunEntry) {
//
//                try {
//
//                    Queue::push(new FetchPaySlip([
//                        'headers' => $headers,
//                        'employerId' => $payRunEntry->employerId ?? null,
//                        'payPeriod' => $payRunEntry->payPeriod ?? null,
//                        'periodNumber' => $payRunEntry->period ?? null,
//                        'taxYear' => $payRunEntry->taxYear ?? null,
//                        'payRunEntry' => $payRunEntry ?? null
//                    ]));
//
//                } catch (\Throwable $e) {
//                    echo "---- error -----\n";
//                    var_dump($e->getMessage());
//                    Craft::error($e->getMessage(), __METHOD__);
////                    Craft::dd($e);
//                    echo "\n---- end error ----";
//                }
//            }
//        }
//
//        return "success";
//    }
}
