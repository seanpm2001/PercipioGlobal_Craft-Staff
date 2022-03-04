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

use craft\helpers\App;
use craft\helpers\Json;
use percipiolondon\staff\Staff;
use percipiolondon\staff\helpers\Logger;
use percipiolondon\staff\jobs\FetchPayCodesListJob;
use percipiolondon\staff\jobs\FetchPaySchedulesJob;
use percipiolondon\staff\jobs\FetchPaySlipJob;
use percipiolondon\staff\jobs\CreatePayCodeJob;
use percipiolondon\staff\jobs\CreatePayRunJob;
use percipiolondon\staff\jobs\CreatePayRunEntryJob;

use percipiolondon\staff\records\PayRun as PayRunRecord;

use Craft;
use craft\base\Component;
use yii\base\BaseObject;

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
class PayRun extends Component
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

    public function savePayCode(array $payCode, string $progress = "")
    {
        $logger = new Logger();
        $logger->stdout($progress."✓ Save pension ...", $logger::RESET);
        $logger->stdout(" done" . PHP_EOL, $logger::FG_GREEN);
    }

    public function fetchPayRunSchedule(array $employer)
    {
        $payRuns = $employer['metadata']['payruns'] ?? [];
        $this->fetchPayRun($payRuns, $employer);
//        $queue = Craft::$app->getQueue();
//        $queue->push(new FetchPaySchedulesJob([
//            'description' => 'Fetch pay schedules',
//            'criteria' => [
//                'employer' => $employer,
//            ]
//        ]));
    }

    public function fetchPayRun(array $payRuns, array $employer)
    {
        $queue = Craft::$app->getQueue();
        $queue->push(new CreatePayRunJob([
            'description' => 'Fetch pay runs',
            'criteria' => [
                'payRuns' => $payRuns,
                'employer' => $employer,
            ]
        ]));
    }

    public function savePayRun(array $payRun, string $payRunUrl, array $employer, string $progress): void
    {
        $logger = new Logger();
        $logger->stdout($progress."✓ Save pay run of " .$employer['name'] . ' ' . $payRun['taxYear'] .  ' / ' . $payRun['taxMonth'] . '...', $logger::RESET);
        $logger->stdout(" done" . PHP_EOL, $logger::FG_GREEN);

//        $payRunRecord = PayRunRecord::findOne(['url' => $url]);

        //temporarly
        $queue = Craft::$app->getQueue();
        $queue->push(new CreatePayRunEntryJob([
            'criteria' => [
                'payRunEntries' => $payRun['entries'],
                'employer' => $employer,
            ]
        ]));

        // CREATE PAYRUN IF NOT EXISTS
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

//            if($success) {
//                Craft::info("Saving pay run entries and log");
//
//                $this->savePayRunLog($payRun, $url, $payRunRecord->id);
//
//                // GET PAYRUNENTRY FROM PAYRUN
//                Queue::push(new CreatePayRunEntryJob([
//                    'criteria' => [
//                        'payRunEntries' => $payRun['entries'],
//                        'payRunId' => $payRunRecord->id,
//                        'employer' => $employer,
//                    ]
//                ]));
//            }else{
//                Craft::error($payRunRecord->errors);
//            }
//        }
    }

    public function fetchPaySlip(array $payRunEntry, array $employer)
    {
        $queue = Craft::$app->getQueue();
        $queue->push(new FetchPaySlipJob([
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
        $logger->stdout("✓ Save pay slip of " . $this->criteria['payRunEntry']['personalDetails']['firstName'] . " " . $this->criteria['payRunEntry']['personalDetails']['lastName'] . '...', $logger::RESET);
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
        $logger->stdout($progress."✓ Save pay run log " .$employeeName . '...', $logger::RESET);
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

    public function savePayRunEntry(array $payRunEntryData, string $employee, array $employer, string $progress)
    {
        $logger = new Logger();

        $logger->stdout($progress."✓ Save pay run entry for " . $employee . '...', $logger::RESET);
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


//    /**
//     * This function can literally be anything you want, and you can have as many service
//     * functions as you want
//     *
//     * From any other plugin file, call it like this:
//     *
//     *     Staff::$plugin->payRun->exampleService()
//     *
//     * @return mixed
//     */
//    public function fetch()
//    {
//        $api = Craft::parseEnv(Staff::$plugin->getSettings()->staffologyApiKey);
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
//        $api = Craft::parseEnv(Staff::$plugin->getSettings()->staffologyApiKey);
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
