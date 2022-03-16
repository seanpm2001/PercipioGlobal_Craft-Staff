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
use craft\queue\QueueInterface;
use GuzzleHttp\Exception\GuzzleException;
use percipiolondon\staff\db\Table;
use percipiolondon\staff\elements\Employer;
use percipiolondon\staff\elements\PayRun;
use percipiolondon\staff\elements\PayRunEntry;
use percipiolondon\staff\helpers\Logger;
use percipiolondon\staff\helpers\Security as SecurityHelper;
use percipiolondon\staff\helpers\Csv as CsvHelper;
use percipiolondon\staff\jobs\FetchPayCodesListJob;
use percipiolondon\staff\jobs\FetchPaySchedulesJob;
use percipiolondon\staff\jobs\FetchPaySlipJob;
use percipiolondon\staff\jobs\CreatePayCodeJob;
use percipiolondon\staff\jobs\CreatePayRunJob;
use percipiolondon\staff\jobs\CreatePayRunEntryJob;

use percipiolondon\staff\records\Employee as EmployeeRecord;
use percipiolondon\staff\records\Employer as EmployerRecord;
use percipiolondon\staff\records\EmploymentDetails;
use percipiolondon\staff\records\PayCode as PayCodeRecord;
use percipiolondon\staff\records\PayLine as PayLineRecord;
use percipiolondon\staff\records\PayOption as PayOptionRecord;
use percipiolondon\staff\records\PayRun as PayRunRecord;
use percipiolondon\staff\records\PayRunEntry as PayRunEntryRecord;

use Craft;
use craft\base\Component;
use percipiolondon\staff\records\PayRunLog;
use percipiolondon\staff\records\PayRunTotals;
use percipiolondon\staff\records\PersonalDetails;
use percipiolondon\staff\Staff;
use craft\helpers\Json;
use yii\db\Exception;
use yii\db\Query;
use yii\queue\redis\Queue as RedisQueue;

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


    /* GETTERS */
    public function getPayRunsOfEmployer(int $employerId): array
    {
        $query = new Query();
        $query->from(Table::PAYRUN)
            ->where('employerId = '.$employerId)
            ->all();
        $command = $query->createCommand();
        $payRunQuery = $command->queryAll();

        $payRuns = [];

        foreach($payRunQuery as $payRun) {

            $query = new Query();
            $query->from(Table::PAYRUN_TOTALS)
                ->where('id = '.$payRun['totalsId'])
                ->all();
            $command = $query->createCommand();
            $totals = $command->queryOne();

            $payRun['totals'] = $this->_parseTotals($totals);

            $payRuns[] = $payRun;
        }

        return $payRuns;
    }

    public function getPayRunById(int $payRunId): array
    {
        $query = new Query();
        $query->from(Table::PAYRUN)
            ->where('id = '.$payRunId)
            ->all();
        $command = $query->createCommand();
        $payRunQuery = $command->queryAll();

        $payRuns = [];

        foreach($payRunQuery as $payRun) {

            //totalsId
            $query = new Query();
            $query->from(Table::PAYRUN_TOTALS)
                ->where('id = '.$payRun['totalsId'])
                ->one();
            $command = $query->createCommand();
            $totals = $command->queryOne();

            $payRun['totals'] = $this->_parseTotals($totals);

            //entries
            $query = new Query();
            $query->from(Table::PAYRUN_ENTRIES)
                ->where('payRunId = '.$payRun['id'])
                ->all();
            $command = $query->createCommand();
            $payRunEntries = $command->queryAll();

            $payRun['entries'] = [];

            foreach($payRunEntries as $entry) {

                //pdf
                $entry['pdf'] = SecurityHelper::decrypt($entry['pdf'] ?? '');

                //totals
                $query = new Query();
                $query->from(Table::PAYRUN_TOTALS)
                    ->where('id = '.$entry['totalsId'])
                    ->one();
                $command = $query->createCommand();
                $totals = $command->queryOne();

                $entry['totals'] = $this->_parseTotals($totals);


                //totalsYtd
                $query = new Query();
                $query->from(Table::PAYRUN_TOTALS)
                    ->where('id = '.$entry['totalsYtdId'])
                    ->one();
                $command = $query->createCommand();
                $totals = $command->queryOne();

                $entry['totalsYtd'] = $this->_parseTotals($totals);


                //payOptions
                $query = new Query();
                $query->from(Table::PAY_OPTIONS)
                    ->where('id = '.$entry['payOptionsId'])
                    ->one();
                $command = $query->createCommand();
                $payOptions = $command->queryOne();

                $entry['payOptions'] = $this->parsePayOptions($payOptions);

                //payLines
                $query = new Query();
                $query->from(Table::PAY_LINES)
                    ->where('payOptionsId = '.$entry['payOptionsId'])
                    ->all();
                $command = $query->createCommand();
                $payLines = $command->queryAll();

                $entry['payOptions']['regularPayLines'] = [];

                foreach($payLines as $payLine){
                    $entry['payOptions']['regularPayLines'][] = $this->_parsePayLines($payLine);
                }

                $payRun['entries'][] = $entry;
            }

            $payRuns[] = $payRun;
        }

        return $payRuns;
    }

    public function getCsvTemplate(int $payRunId) :void
    {
        // fetch pay run
        $payRunQuery = PayRunRecord::findOne($payRunId);
        $payRunQuery = $payRunQuery ? $payRunQuery->toArray() : [];

        // fetch pay run entries
        $payRunId = $payRunQuery['id'] ?? null;
        $payRunEntries = PayRunEntryRecord::findAll(['payRunId' => $payRunId]);

        // fetch employer
        $employer = EmployerRecord::findOne($payRunQuery['employerId'] ?? null);
        $employer = $employer ? $employer->toArray() : [];
        $employer = Staff::$plugin->employers->parseEmployer($employer);

        // fetch pay codes
        $payRunEmployerId = $employer['id'] ?? null;
        $payCodes = PayCodeRecord::find()->where(['employerId' => $payRunEmployerId, 'isSystemCode' => 0])->all();

        //create pay codes for all entries
        $payCodeKeys = array_map(function($code){ return $code['code']; },$payCodes);
        sort($payCodeKeys);

        $csvEntries = [];

        //fill in data
        foreach($payRunEntries as $entry) {

            $employee = EmployeeRecord::findOne($entry['employeeId'] ?? null);
            $employee = $employee ? $employee->toArray() : [];

            //personalDetails
            $personalDetails = PersonalDetails::findOne($employee['personalDetailsId'] ?? null);
            $personalDetails = $personalDetails ? $personalDetails->toArray() : null;

            //employmentDetails
            $employmentDetails = EmploymentDetails::findOne($employee['employmentDetailsId'] ?? null);
            $employmentDetails = $employmentDetails ? $employmentDetails->toArray() : null;

            //totals
            $totals = PayRunTotals::findOne($entry['totalsId'] ?? null);
            $totals = $totals ? $totals->toArray() : null;

            //payLines
            $payLines = PayLineRecord::find()->where(['payOptionsId' => $entry['payOptionsId'] ?? null])->all();

            // decrypt values
            $personalDetails = Staff::$plugin->employees->parsePersonalDetails($personalDetails);
            $employmentDetails = Staff::$plugin->employees->parseEmploymentDetails($employmentDetails);
            $totals = $this->_parseTotals($totals);

            //CSV structure
            $payRunEntry = [];
            $payRunEntry['id'] = (int)($entry['id'] ?? null);
            $payRunEntry['name'] = ($personalDetails['title'] . ' ' ?? null).($personalDetails['firstName'] ?? null) . ' ' . ($personalDetails['lastName'] ?? null);
            $payRunEntry['niNumber'] = $personalDetails['niNumber'] ?? null;
            $payRunEntry['payrollCode'] = $employmentDetails['payrollCode'] ?? null;
            $payRunEntry['gross'] = (float)($totals['gross'] ?? 0);
            $payRunEntry['netPay'] = (float)($totals['netPay'] ?? 0);
            $payRunEntry['totalCost'] = (float)($totals['totalCost'] ?? 0);

            //set all the pay run codes dynamic to payRunEntry with default value
            foreach($payCodeKeys as $payCodeKey) {
                $payRunEntry[$payCodeKey] = '';
                $payRunEntry['description_'.$payCodeKey] = '';
            }

            //overwrite custom pay lines
            foreach($payLines as $payLine){
                $payLine = $payLine->toArray();
                $payLine = $this->_parsePayLines($payLine);

                if($payLine && in_array($payLine['code'], $payCodeKeys, true)){

                    $payRunEntry[$payLine['code']] = (float)($payLine['value'] ?? '');
                    $payRunEntry['description_'.$payLine['code']] = $payLine['description'] ?? '';

                }
            }

            $csvEntries[] = $payRunEntry;

        }

        usort($csvEntries, function($a, $b){
            return $a['payrollCode'] > $b['payrollCode'];
        });

        Craft::dd($csvEntries);

        CsvHelper::arrayToCsv($csvEntries,'pay-'.($employer['slug'] ?? 'x').'-'.($payRunQuery['taxMonth'] ?? 'x').'-'.strtolower($payRunQuery['taxYear']) ?? 'x');
    }

    public function setPayRunEntry(array $entries) :array
    {
        $savedEntries = [];

        foreach($entries as $entry) {
            $payRunEntry = PayRunEntryRecord::findOne($entry['id'] ?? null);

            if($payRunEntry){
                $employer = EmployerRecord::findOne($payRunEntry['employerId'] ?? null);

                if($employer){
                    $employer = $employer->toArray();

                    $id = $payRunEntry['staffologyId'] ?? null;
                    $employerId = $employer['staffologyId'] ?? null;
                    $taxYear = $payRunEntry['taxYear'] ?? null;
                    $payPeriod = $payRunEntry['payPeriod'] ?? 'Monthly';
                    $period = $payRunEntry['period'] ?? null;

                    $api = App::parseEnv(Staff::$plugin->getSettings()->apiKeyStaffology);
                    $base_url = 'https://api.staffology.co.uk/employers/'.$employerId.'/payrun/'.$taxYear.'/'.$payPeriod.'/'.$period.'/'.$id;
                    $credentials = base64_encode('staff:'.$api);
                    $headers = [
                        'headers' => [
                            'Authorization' => 'Basic ' . $credentials,
                        ],
                    ];

                    $client = new \GuzzleHttp\Client();

                    try {
                        $response = $client->get($base_url, $headers);
                        $payRunEntryData = Json::decodeIfJson($response->getBody()->getContents(), true);

                        $payRunEntry = $this->savePayRunEntry($payRunEntryData, $employer, $payRunEntry['payRunId']);

                        if($payRunEntry) {
                            $savedEntries[] = $payRunEntry;
                        }

                    } catch (\Exception $e) {

                        Craft::error($e->getMessage(), __METHOD__);

                    }
                }
            }
        }

        return $savedEntries;
    }






    /* FETCHES */
    public function fetchPayCodesList(array $employer): void
    {
        $queue = Craft::$app->getQueue();
        $queue->push(new FetchPayCodesListJob([
            'description' => 'Fetch pay codes',
            'criteria' => [
                'employer' => $employer
            ]
        ]));
    }

    public function fetchPayCodes(array $payCodes, array $employer): void
    {
        $queue = Craft::$app->getQueue();
        $queue->push(new CreatePayCodeJob([
            'description' => 'Save pay codes',
            'criteria' => [
                'payCodes' => $payCodes,
                'employer' => $employer
            ]
        ]));
    }

    public function fetchPayRuns(array $payRuns, array $employer): void
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

    public function fetchPayRunByEmployer(array $employer): void
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

    public function fetchPayRunByPayRunId(int $payRunId): void
    {
        $payRun = PayRunRecord::findOne($payRunId);

        if($payRun) {
            $employerId = $payRun['employerId'] ?? null;
            $employer = EmployerRecord::findOne($employerId);

            $queue = Craft::$app->getQueue();
            $queue->push(new CreatePayRunJob([
                'description' => 'Fetch pay runs',
                'criteria' => [
                    'payRuns' => [$payRun],
                    'employer' => $employer,
                ]
            ]));
        }
    }

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
    public function savePayCode(array $payCode, array $employer): void
    {
        $logger = new Logger();
        $logger->stdout("✓ Save pay code " . $payCode['code'] . "...", $logger::RESET);

        $employerRecord = EmployerRecord::findOne(['staffologyId' => $employer['id']]);
        $payCodeRecord = PayCodeRecord::findOne(['code' => $payCode['code'], 'employerId' => $employerRecord->id ?? null]);

        if(!$payCodeRecord){
            $payCodeRecord = new PayCodeRecord();
        }
        $payCodeRecord->title = $payCode['title'] ?? null;
        $payCodeRecord->code = $payCode['code'] ?? null;
        $payCodeRecord->employerId = $employerRecord->id ?? null;
        $payCodeRecord->defaultValue = SecurityHelper::encrypt($payCode['defaultValue'] ?? '');
        $payCodeRecord->isSystemCode = $payCode['isSystemCode'] ?? null;
        $success = $payCodeRecord->save();

        if($success) {

            $logger->stdout(" done" . PHP_EOL, $logger::FG_GREEN);

        }else{
            $logger->stdout(" failed" . PHP_EOL, $logger::FG_RED);

            $errors = "";

            foreach($payCodeRecord->errors as $err) {
                $errors .= implode(',', $err);
            }

            $logger->stdout($errors . PHP_EOL, $logger::FG_RED);
            Craft::error($payCodeRecord->errors, __METHOD__);
        }
    }

    public function savePayRun(array $payRun, string $payRunUrl, array $employer): void
    {
        $logger = new Logger();
        $logger->stdout("✓ Save pay run of " .$employer['name'] . ' ' . $payRun['taxYear'] .  ' / ' . $payRun['taxMonth'] . '...', $logger::RESET);

        $payRunRecord = PayRun::findOne(['url' => $payRunUrl]);

        try {
            if(!$payRunRecord) {
                $payRunRecord = new PayRun();

                //foreign keys
                $totalsId = $payRunRecord->totalsId;
            } else {
                //foreign keys
                $totalsId = null;
            }

            //foreign keys
            $totals = Staff::$plugin->payRuns->saveTotals( $payRun['totals'] ?? [], $totalsId);
            $emp = Employer::findOne(['staffologyId' => $employer['id'] ?? null]);

            $payRunRecord->employerId = $emp['id'] ?? null;
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
            $payRunRecord->totalsId = $totals->id ?? null;
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
                        'payRun' => $payRunRecord,
                        'payRunEntries' => $payRun['entries'],
                        'employer' => $employer,
                    ]
                ]));

                $logger->stdout(" done" . PHP_EOL, $logger::FG_GREEN);

                $this->savePayRunLog($payRun, $payRunUrl, $payRunRecord->id, $employer['id']);

            }else{
                $logger->stdout(" failed" . PHP_EOL, $logger::FG_RED);

                $errors = "";

                foreach($payRunRecord->errors as $err) {
                    $errors .= implode(',', $err);
                }

                $logger->stdout($errors . PHP_EOL, $logger::FG_RED);
                Craft::error($payRunRecord->errors, __METHOD__);
            }

        } catch (\Exception $e) {

            $logger = new Logger();
            $logger->stdout(PHP_EOL, $logger::RESET);
            $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
            Craft::error($e->getMessage(), __METHOD__);
        }
    }

    public function savePayRunLog(array $payRun, string $url, string $payRunId, string $employerId): bool
    {
        $logger = new Logger();
        $logger->stdout('✓ Save pay run log ...', $logger::RESET);

        $payRunLog = new PayRunLog();
        $employer = EmployerRecord::findOne(['staffologyId' => $employerId]);

        $payRunLog->employerId = $employer->id ?? null;

        $payRunLog->taxYear = $payRun['taxYear'] ?? '';
        $payRunLog->employeeCount = $payRun['employeeCount'] ?? null;
        $payRunLog->lastPeriodNumber = $payRun['employeeCount'] ?? null;
        $payRunLog->url = $url ?? '';
        $payRunLog->payRunId = $payRunId;

        $success = $payRunLog->save(true);

        if($success) {

            $logger->stdout(" done" . PHP_EOL, $logger::FG_GREEN);

        }else{
            $logger->stdout(" failed" . PHP_EOL, $logger::FG_RED);

            $errors = "";

            foreach($payRunLog->errors as $err) {
                $errors .= implode(',', $err);
            }

            $logger->stdout($errors . PHP_EOL, $logger::FG_RED);
            Craft::error($payRunLog->errors, __METHOD__);
        }

        return $success;
    }

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

            $totals = $this->saveTotals($payRunEntryData['totals'], $totalsId);
            $totalsYtd = $this->saveTotals($payRunEntryData['totalsYtd'], $totalsYtdId);
            $payOptions = $this->savePayOptions($payRunEntryData['payOptions'], $payOptionsId);
            $employee = EmployeeRecord::findOne(['staffologyId' => $payRunEntryData['employee']['id'] ?? null]);

            $employerRecord = is_int($employer['id'] ?? null) ? $employer : EmployerRecord::findOne(['staffologyId' => $employer['id'] ?? null]);

            //save
            $payRunEntryRecord->employerId = $employerRecord['id'] ?? null;
            $payRunEntryRecord->employeeId = $employee->id ?? null;
            $payRunEntryRecord->payRunId = $payRunId ?? null;
            $payRunEntryRecord->payOptionsId = $payOptions->id ?? null;
            $payRunEntryRecord->totalsId = $totals->id ?? null;
            $payRunEntryRecord->totalsYtdId = $totalsYtd->id ?? null;
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

            if($success){
                $logger->stdout(" done" . PHP_EOL, $logger::FG_GREEN);
            } else {
                $logger->stdout(" failed" . PHP_EOL, $logger::FG_RED);

                $errors = "";

                foreach($payRunEntryRecord->errors as $err) {
                    $errors .= implode(',', $err);
                }

                $logger->stdout($errors . PHP_EOL, $logger::FG_RED);
                Craft::error($payRunEntryRecord->errors, __METHOD__);
            }

            return $payRunEntryRecord;

        } catch (\Exception $e) {

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
        $logger->stdout("✓ Save pay slip of ". $payRunEntry['employee']['name'] ?? '' ."...", $logger::RESET);

        $record = PayRunEntryRecord::findOne(['staffologyId' => $payRunEntry['id']]);

        if($paySlip['content'] && $record)
        {
            $record->pdf = SecurityHelper::encrypt($paySlip['content'] ?? '');

            $success = $record->save();

            if($success) {
                $logger->stdout(" done" . PHP_EOL, $logger::FG_GREEN);
            } else {
                $logger->stdout(PHP_EOL, $logger::RESET);
                $logger->stdout("The payslip couldn't be created for ". $payRunEntry['employee']['name'] ?? '' . PHP_EOL, $logger::FG_RED);
                Craft::error("The payslip couldn't be created for ". $payRunEntry['employee']['name'] ?? '', __METHOD__);
            }
        }
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

        $success = $record->save();

        if($success) {
            return $record;
        }

        $errors = "";

        foreach($record->errors as $err) {
            $errors .= implode(',', $err);
        }

        $logger = new Logger();
        $logger->stdout($errors . PHP_EOL, $logger::FG_RED);
        Craft::error($record->errors, __METHOD__);

    }

    public function savePayOptions(array $payOptions, int $payOptionsId = null): PayOptionRecord
    {
        if($payOptionsId) {
            $record = PayOptionRecord::findOne($payOptionsId);

            if (!$record) {
                throw new Exception('Invalid pay options ID: ' . $payOptionsId);
            }

        }else{
            $record = new PayOptionRecord();
        }

        $record->period = $payOptions['period'] ?? null;
        $record->ordinal = $payOptions['ordinal'] ?? null;
        $record->payAmount = SecurityHelper::encrypt($totals['payAmount'] ?? '');
        $record->basis = $payOptions['basis'] ?? 'Monthly';
        $record->nationalMinimumWage = $payOptions['nationalMinimumWage'] ?? null;
        $record->payAmountMultiplier = $payOptions['payAmountMultiplier'] ?? null;
        $record->baseHourlyRate = SecurityHelper::encrypt($totals['baseHourlyRate'] ?? '');
        $record->autoAdjustForLeave = $payOptions['autoAdjustForLeave'] ?? null;
        $record->method = $payOptions['method'] ?? null;
        $record->payCode = $payOptions['payCode'] ?? null;
        $record->withholdTaxRefundIfPayIsZero = $payOptions['withholdTaxRefundIfPayIsZero'] ?? null;
        $record->mileageVehicleType = $payOptions['mileageVehicleType'] ?? null;
        $record->mapsMiles = $payOptions['mapsMiles'] ?? null;

        $success = $record->save();

        if($success) {

            //save pay lines
            foreach($payOptions['regularPayLines'] ?? [] as $payLine){
                $this->savePayLines($payLine, $record->id);
            }

            return $record;
        }

        $errors = "";
        foreach($record->errors as $err) {
            $errors .= implode(',', $err);
        }

        $logger = new Logger();
        $logger->stdout($errors . PHP_EOL, $logger::FG_RED);
        Craft::error($record->errors, __METHOD__);
    }

    public function savePayLines(array $payLine, int $payOptionsId = null): void
    {
        $record = PayLineRecord::findOne(['payOptionsId' => $payOptionsId, 'code' => $payLine['code'] ?? null]);

        if(!$record) {
            $record = new PayLineRecord();
        }

        $record->payOptionsId = $payOptionsId ?? null;
        $record->value = SecurityHelper::encrypt($payLine['value'] ?? '');
        $record->rate = SecurityHelper::encrypt($payLine['rate'] ?? '');
        $record->description = $payLine['description'] ?? null;
        $record->attachmentOrderId = $payLine['attachmentOrderId'] ?? null;
        $record->pensionId = $payLine['pensionId'] ?? null;
        $record->code = $payLine['code'] ?? null;

        $record->save();
    }





    /* UPDATES */
    public function updatePayRunEntry(string $payPeriod, int $employer, int $payRunId, array $payRunEntryUpdate): void
    {
        $employer = EmployerRecord::findOne($employer);

        Craft::dd('stop');

        if($employer){
            $api = App::parseEnv(Staff::$plugin->getSettings()->apiKeyStaffology);
            $base_url = 'https://api.staffology.co.uk/employers/'.$employer['staffologyId'].'/payrun/'.$payPeriod.'/importpay?linesOnly=true';
//            $base_url = 'https://api.staffology.co.uk/employers/'.$employer['staffologyId'].'/payrun/'.$payRunEntry['taxYear'].'/'.$payRunEntry['payPeriod'].'/'.$payRunEntry['period'].'/'.$payRunEntry['staffologyId'];
            $credentials = base64_encode('staff:'.$api);
            $client = new \GuzzleHttp\Client([
                'headers' => [
                    'Authorization' => 'Basic ' . $credentials,
                ],
            ]);

            var_dump($base_url);
            echo "<br/><br/>";
            var_dump(json_encode($payRunEntryUpdate));
            echo "<br/>";
            Craft::dd((array)$payRunEntryUpdate);

            try {
                $response = $client->post(
                    $base_url,
                    [
                        'json' => $payRunEntryUpdate,
                    ]
                );


                $this->fetchPayRunByPayRunId($payRunId);

                $queue = Craft::$app->getQueue();
                if ($queue instanceof QueueInterface) {
                    $queue->run();
                } elseif ($queue instanceof RedisQueue) {
                    $queue->run(false);
                }


            } catch (GuzzleException $e) {

                Craft::dd($e->getMessage());

                Craft::error($e->getMessage(), __METHOD__);

            }
        }
    }




    /* PARSE SECURITY VALUES */
    private function _parseTotals(array $totals) :array
    {
        $totals['basicPay'] = SecurityHelper::decrypt($totals['basicPay'] ?? '');
        $totals['gross'] = SecurityHelper::decrypt($totals['gross'] ?? '');
        $totals['grossForNi'] = SecurityHelper::decrypt($totals['grossForNi'] ?? '');
        $totals['grossNotSubjectToEmployersNi'] = SecurityHelper::decrypt($totals['grossNotSubjectToEmployersNi'] ?? '');
        $totals['grossForTax'] = SecurityHelper::decrypt($totals['grossForTax'] ?? '');
        $totals['employerNi'] = SecurityHelper::decrypt($totals['employerNi'] ?? '');
        $totals['employeeNi'] = SecurityHelper::decrypt($totals['employeeNi'] ?? '');
        $totals['tax'] = SecurityHelper::decrypt($totals['tax'] ?? '');
        $totals['netPay'] = SecurityHelper::decrypt($totals['netPay'] ?? '');
        $totals['adjustments'] = SecurityHelper::decrypt($totals['adjustments'] ?? '');
        $totals['additions'] = SecurityHelper::decrypt($totals['additions'] ?? '');
        $totals['takeHomePay'] = SecurityHelper::decrypt($totals['takeHomePay'] ?? '');
        $totals['nonTaxOrNICPmt'] = SecurityHelper::decrypt($totals['nonTaxOrNICPmt'] ?? '');
        $totals['studentLoanRecovered'] = SecurityHelper::decrypt($totals['studentLoanRecovered'] ?? '');
        $totals['postgradLoanRecovered'] = SecurityHelper::decrypt($totals['postgradLoanRecovered'] ?? '');
        $totals['pensionableEarnings'] = SecurityHelper::decrypt($totals['pensionableEarnings'] ?? '');
        $totals['pensionablePay'] = SecurityHelper::decrypt($totals['pensionablePay'] ?? '');
        $totals['nonTierablePay'] = SecurityHelper::decrypt($totals['nonTierablePay'] ?? '');
        $totals['employeePensionContribution'] = SecurityHelper::decrypt($totals['employeePensionContribution'] ?? '');
        $totals['employeePensionContributionAvc'] = SecurityHelper::decrypt($totals['employeePensionContributionAvc'] ?? '');
        $totals['employerPensionContribution'] = SecurityHelper::decrypt($totals['employerPensionContribution'] ?? '');
        $totals['empeePenContribnsNotPaid'] = SecurityHelper::decrypt($totals['empeePenContribnsNotPaid'] ?? '');
        $totals['empeePenContribnsPaid'] = SecurityHelper::decrypt($totals['empeePenContribnsPaid'] ?? '');
        $totals['attachmentOrderDeductions'] = SecurityHelper::decrypt($totals['attachmentOrderDeductions'] ?? '');
        $totals['cisDeduction'] = SecurityHelper::decrypt($totals['cisDeduction'] ?? '');
        $totals['cisVat'] = SecurityHelper::decrypt($totals['cisVat'] ?? '');
        $totals['cisUmbrellaFee'] = SecurityHelper::decrypt($totals['cisUmbrellaFee'] ?? '');
        $totals['cisUmbrellaFeePostTax'] = SecurityHelper::decrypt($totals['cisUmbrellaFeePostTax'] ?? '');
        $totals['umbrellaFee'] = SecurityHelper::decrypt($totals['umbrellaFee'] ?? '');
        $totals['totalCost'] = SecurityHelper::decrypt($totals['totalCost'] ?? '');

        return $totals;
    }

    public function parsePayOptions(array $payOptions): array
    {
        $payOptions['payAmount'] = SecurityHelper::decrypt($payOptions['payAmount'] ?? '');
        $payOptions['baseHourlyRate'] = SecurityHelper::decrypt($payOptions['baseHourlyRate'] ?? '');

        return $payOptions;
    }

    private function _parsePayLines(array $payLine): array
    {
        $payLine['value'] = SecurityHelper::decrypt($payLine['value'] ?? '');
        $payLine['rate'] = SecurityHelper::decrypt($payLine['rate'] ?? '');

        return $payLine;
    }
}
