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

use percipiolondon\staff\db\Table;
use percipiolondon\staff\elements\PayRun;
use percipiolondon\staff\elements\PayRunEntry;
use percipiolondon\staff\helpers\Logger;
use percipiolondon\staff\helpers\Security as SecurityHelper;
use percipiolondon\staff\jobs\FetchPayCodesListJob;
use percipiolondon\staff\jobs\FetchPaySchedulesJob;
use percipiolondon\staff\jobs\FetchPaySlipJob;
use percipiolondon\staff\jobs\CreatePayCodeJob;
use percipiolondon\staff\jobs\CreatePayRunJob;
use percipiolondon\staff\jobs\CreatePayRunEntryJob;

use percipiolondon\staff\records\Employer as EmployerRecord;
use percipiolondon\staff\records\PayLine as PayLineRecord;
use percipiolondon\staff\records\PayOption as PayOptionRecord;
use percipiolondon\staff\records\PayRun as PayRunRecord;
use percipiolondon\staff\records\PayRunEntry as PayRunEntryRecord;

use Craft;
use craft\base\Component;
use percipiolondon\staff\records\PayRunLog;
use percipiolondon\staff\records\PayRunTotals;
use yii\db\Exception;
use yii\db\Query;

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

                $entry['payOptions'] = $this->_parsePayOptions($payOptions);

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

                Craft::dd($entry);
            }



            $payRuns[] = $payRun;
        }

        Craft::dd($payRuns);

        return $payRuns;
    }






    /* FETCHES */
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






    /* SAVES */
    public function savePayCode(array $payCode)
    {
        $logger = new Logger();
        $logger->stdout("✓ Save pay code " . $payCode['code'] . "...", $logger::RESET);
        $logger->stdout(" done" . PHP_EOL, $logger::FG_GREEN);
    }

    public function savePayRun(array $payRun, string $payRunUrl, array $employer): void
    {
        $logger = new Logger();

        $payRunRecord = PayRunRecord::findOne(['url' => $payRunUrl]);

        // CREATE PAYRUN IF NOT EXISTS
        if(!$payRunRecord) {
            $logger->stdout("✓ Save pay run of " .$employer['name'] . ' ' . $payRun['taxYear'] .  ' / ' . $payRun['taxMonth'] . '...', $logger::RESET);

            $payRunRecord = new PayRun();

            $payRunRecord->employerId = $employer['id'] ?? null;
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
        }
    }

    public function savePayRunLog(array $payRun, string $url, string $payRunId, string $employerId)
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
    }

    public function savePayRunEntry(array $payRunEntryData, array $employer, PayRun $payRun)
    {
        $logger = new Logger();

        $logger->stdout("✓ Save pay run entry for " . $payRunEntryData['employee']['name'] ?? '' . '...', $logger::RESET);

        $payRunEntry = new PayRunEntry();
        $payRunRecord = PayRunRecord::findOne(['url' => $payRun['url']]); //fetch payrun from custom table instead of elements

        $payRunEntry->employerId = $employer['id'] ?? null;
        $payRunEntry->employeeId = $payRunEntryData['employee']['id'] ?? null;
        $payRunEntry->payRunId = $payRunRecord->id ?? null;

        $payRunEntry->totals = $payRunEntryData['totals'] ?? '';
        $payRunEntry->totalsYtd = $payRunEntryData['totalsYtd'] ?? '';
        $payRunEntry->umbrellaPayment = $payRunEntryData['umbrellaPayment'] ?? '';
        $payRunEntry->priorPayrollCode = $payRunEntryData['priorPayrollCode'] ?? '';
        $payRunEntry->payOptions = $payRunEntryData['payOptions'] ?? '';
        $payRunEntry->nationalInsuranceCalculation = $payRunEntryData['nationalInsuranceCalculation'] ?? '';
        $payRunEntry->fps = $payRunEntryData['fps'] ?? '';
        $payRunEntry->employee = $payRunEntryData['employee'] ?? '';
        $payRunEntry->pensionSummary = $payRunEntryData['pensionSummary'] ?? '';

        $payRunEntry->periodOverrides = $payRunEntryData['periodOverrides'] ?? '';
        $payRunEntry->totalsYtdOverrides = $payRunEntryData['totalsYtdOverrides'] ?? '';

        $payRunEntry->staffologyId = $payRunEntryData['id'] ?? null;
        $payRunEntry->taxYear = $payRunEntryData['taxYear'] ?? null;
        $payRunEntry->startDate = $payRunEntryData['startDate'] ?? null;
        $payRunEntry->endDate = $payRunEntryData['endDate'] ?? null;
        $payRunEntry->note = $payRunEntryData['note'] ?? '';
        $payRunEntry->bacsSubReference = $payRunEntryData['bacsSubReference'] ?? '';
        $payRunEntry->bacsHashcode = $payRunEntryData['bacsHashcode'] ?? '';
        $payRunEntry->percentageOfWorkingDaysPaidAsNormal = $payRunEntryData['percentageOfWorkingDaysPaidAsNormal'] ?? null;
        $payRunEntry->workingDaysNotPaidAsNormal = $payRunEntryData['workingDaysNotPaidAsNormal'] ?? null;
        $payRunEntry->payPeriod = $payRunEntryData['payPeriod'] ?? null;
        $payRunEntry->ordinal = $payRunEntryData['ordinal'] ?? null;
        $payRunEntry->period = $payRunEntryData['period'] ?? null;
        $payRunEntry->isNewStarter = $payRunEntryData['isNewStarter'] ?? null;
        $payRunEntry->unpaidAbsence = $payRunEntryData['unpaidAbsence'] ?? null;
        $payRunEntry->hasAttachmentOrders = $payRunEntryData['hasAttachmentOrders'];
        $payRunEntry->paymentDate = $payRunEntryData['paymentDate'] ?? null;
        $payRunEntry->forcedCisVatAmount = $payRunEntryData['forcedCisVatAmount'] ?? null;
        $payRunEntry->holidayAccrued = $payRunEntryData['holidayAccrued'] ?? null;
        $payRunEntry->state = $payRunEntryData['state'] ?? '';
        $payRunEntry->isClosed = $payRunEntryData['isClosed'] ?? null;
        $payRunEntry->manualNi = $payRunEntryData['manualNi'] ?? null;
        $payRunEntry->aeNotEnroledWarning = $payRunEntryData['aeNotEnroledWarning'] ?? null;
        $payRunEntry->receivingOffsetPay = $payRunEntryData['receivingOffsetPay'] ?? null;
        $payRunEntry->paymentAfterLearning = $payRunEntryData['paymentAfterLearning'] ?? null;
//        $payRunEntry->pdf = $payRunEntryData['pdf'];

        $elementsService = Craft::$app->getElements();
        $success = $elementsService->saveElement($payRunEntry);

        if($success) {

            $logger->stdout(" done" . PHP_EOL, $logger::FG_GREEN);

        }else{
            $logger->stdout(" failed" . PHP_EOL, $logger::FG_RED);

            $errors = "";

            foreach($payRunEntry->errors as $err) {
                $errors .= implode(',', $err);
            }

            $logger->stdout($errors . PHP_EOL, $logger::FG_RED);
            Craft::error($payRunEntry->errors, __METHOD__);
        }
    }

    public function savePaySlip(array $paySlip, array $payRunEntry)
    {
        $logger = new Logger();
        $logger->stdout("✓ Save pay slip of ". $payRunEntry['employee']['name'] ?? '' ."...", $logger::RESET);

        $record = PayRunEntryRecord::findOne(['staffologyId' => $payRunEntry['id']]);

        if($paySlip['content'] ?? null && $record)
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

    public function savePayLines(array $payLine, int $payOptionsId): void
    {
        $record = new PayLineRecord();

        $record->payOptionsId = $payOptionsId ?? null;
        $record->value = SecurityHelper::encrypt($payLine['value'] ?? '');
        $record->rate = SecurityHelper::encrypt($payLine['rate'] ?? '');
        $record->description = $payLine['description'] ?? null;
        $record->attachmentOrderId = $payLine['attachmentOrderId'] ?? null;
        $record->pensionId = $payLine['pensionId'] ?? null;
        $record->code = $payLine['code'] ?? null;

        $record->save();
    }




    // Private Methods
    // =========================================================================

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

    private function _parsePayOptions(array $payOptions): array
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
