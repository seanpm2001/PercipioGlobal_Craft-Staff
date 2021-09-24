<?php
/**
 * staff-management plugin for Craft CMS 3.x
 *
 * Craft Staff Management provides an HR solution for payroll and benefits
 *
 * @link      http://percipio.london
 * @copyright Copyright (c) 2021 Percipio
 */

namespace percipiolondon\craftstaff\services;

use percipiolondon\craftstaff\Craftstaff;

use Craft;
use craft\base\Component;
use percipiolondon\craftstaff\records\Employer;
use percipiolondon\craftstaff\records\PayRunLog as PayRunLogRecord;
use percipiolondon\craftstaff\records\PayRun as PayRunRecord;
use percipiolondon\craftstaff\records\PayRunEntry as PayRunEntryRecord;

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
 * @package   Craftstaff
 * @since     1.0.0-alpha.1
 */
class PayRun extends Component
{
    private $_headers = null;
    private $_client = null;

    // Public Methods
    // =========================================================================

    /**
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     Craftstaff::$plugin->payRun->exampleService()
     *
     * @return mixed
     */
    public function fetch()
    {
        $result = 'something';
        $api = Craft::parseEnv(Craftstaff::$plugin->getSettings()->staffologyApiKey);
        $credentials = base64_encode("craftstaff:".$api);
        $this->_headers = [
            'headers' => [
                'Authorization' => 'Basic ' . $credentials,
            ],
        ];
        $this->_client = new \GuzzleHttp\Client();

        if ($api) {
            // GET EMPLOYERS
            $employers = Employer::find()->all();

            foreach($employers as $employer) {
                $base_url = "https://api.staffology.co.uk/employers/{$employer->staffologyId}/schedules/{$employer->currentYear}";

                //GET LIST OF PAYSCHEDULES
                try {

                    $response = $this->_client->get($base_url, $this->_headers);
                    $payschedules = json_decode($response->getBody()->getContents(), true);

                    $this->_fetchPayRuns($payschedules, $employer->id);


                } catch (\Throwable $e) {
                    echo "---- error -----\n";
//                    var_dump($e->getMessage(), $e->getLine(), $e->getFile());
                    Craft::dd($e);
                    echo "\n---- end error ----";
                }
            }
            die();
        }

        return $result;
    }

    private function _fetchPayRuns($payschedules, $employerId)
    {
        foreach($payschedules as $schedule) {
            if(count($schedule['payRuns']) > 0) {

                foreach($schedule['payRuns'] as $payRun) {

                    $payRunLog = PayRunLogRecord::findOne(['url' => $payRun['url']]);

                    // SET PAYRUN IF IT HASN'T ALREADY BEEN FETCHED IN PAYRUNLOG
                    if(!$payRunLog) {

                        $response =  $this->_client->get($payRun['url'], $this->_headers);
                        $payRunData = json_decode($response->getBody()->getContents(), true);

                        $this->_savePayRunLog($payRunData, $payRun['url'], $employerId);
                        $this->_savePayRun($payRunData, $payRun['url'], $employerId);
                    }

                }

            }
        }
    }

    private function _savePayRunLog($payRun, $url, $employerId)
    {
        $payRunLog = new PayRunLogRecord();
        $payRunLog->siteId = Craft::$app->getSites()->currentSite->id;
        $payRunLog->taxYear = $payRun['taxYear'] ?? '';
        $payRunLog->employeeCount = $payRun['employeeCount'] ?? null;
        $payRunLog->lastPeriodNumber = $payRun['employeeCount'] ?? null;
        $payRunLog->url = $url ?? '';
        $payRunLog->employerId = $employerId;

//        $payRunLog->save(false);
    }

    private function _savePayRun($payRun, $url, $employerId)
    {
        $payRunRecord = PayRunRecord::findOne(['url' => $url]);

        // CREATE PAYRUN IF NOT EXISTS
        if(!$payRunRecord) {
            $payRunRecord = new PayRunRecord();
            $payRunRecord->siteId = Craft::$app->getSites()->currentSite->id;
            $payRunRecord->staffologyId = "";
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
            $payRunRecord->employerId = $employerId;
            $payRunRecord->url = $url ?? '';

            $payRunRecord->save(false);

            // GET PAYRUNENTRY FROM PAYRUN
            $this->_fetchPayRunEntries($payRun['entries'], $payRunRecord->id, $employerId);
        }
    }

    private function _fetchPayRunEntries($payRunEntries, $payRunId, $employerId)
    {
        foreach($payRunEntries as $payRunEntryData) {

            $payRunEntry = PayRunEntryRecord::findOne(['staffologyId' => $payRunEntryData['id']]);

            // SET PAY RUN ENTRY IF IT DOESN'T EXIST
            if(!$payRunEntry) {

//                @todo: start with testing just with one to see if it works --> then adding it into queue and run

                var_dump($payRunEntryData['url']);

                $response =  $this->_client->get($payRunEntryData['url'], $this->_headers);
                $payRunEntryData = json_decode($response->getBody()->getContents(), true);

                $this->_savePayRunEntry($payRunEntryData, $payRunId, $employerId);
                die();
            }
        }
    }

    private function _savePayRunEntry($payRunEntryData, $payRunId, $employerId)
    {
        $payRunEntry = new PayRunEntryRecord();
        $payRunEntry->siteId = Craft::$app->getSites()->currentSite->id;
        $payRunEntry->employerId = $employerId;
        $payRunEntry->staffologyId = $payRunEntryData->staffologyId;
        $payRunEntry->payrunId = $payRunId;
        $payRunEntry->taxYear = $payRunEntryData->taxYear;
        $payRunEntry->startDate = $payRunEntryData->startDate;
        $payRunEntry->endDate = $payRunEntryData->endDate;
        $payRunEntry->note = $payRunEntryData->note;
        $payRunEntry->bacsSubReference = $payRunEntryData->bacsSubReference;
        $payRunEntry->bacsHashcode = $payRunEntryData->bacsHashcode;
        $payRunEntry->percentageOfWorkingDaysPaidAsNormal = $payRunEntryData->percentageOfWorkingDaysPaidAsNormal;
        $payRunEntry->workingDaysNotPaidAsNormal = $payRunEntryData->workingDaysNotPaidAsNormal;
        $payRunEntry->payPeriod = $payRunEntryData->payPeriod;
        $payRunEntry->ordinal = $payRunEntryData->ordinal;
        $payRunEntry->period = $payRunEntryData->period;
        $payRunEntry->isNewStarter = $payRunEntryData->isNewStarter;
        $payRunEntry->unpaidAbsence = $payRunEntryData->unpaidAbsence;
        $payRunEntry->hasAttachmentOrders = $payRunEntryData->hasAttachmentOrders;
        $payRunEntry->paymentDate = $payRunEntryData->paymentDate;
        $payRunEntry->priorPayrollCode = $payRunEntryData->priorPayrollCode;
        $payRunEntry->payOptions = $payRunEntryData->payOptions;
        $payRunEntry->pensionSummary = $payRunEntryData->pensionSummary;
        $payRunEntry->totals = $payRunEntryData->totals;
        $payRunEntry->periodOverrides = $payRunEntryData->periodOverrides;
        $payRunEntry->totalsYtd = $payRunEntryData->totalsYtd;
        $payRunEntry->totalsYtdOverrides = $payRunEntryData->totalsYtdOverrides;
        $payRunEntry->forcedCisVatAmount = $payRunEntryData->forcedCisVatAmount;
        $payRunEntry->holidayAccured = $payRunEntryData->holidayAccured;
        $payRunEntry->state = $payRunEntryData->state;
        $payRunEntry->isClosed = $payRunEntryData->isClosed;
        $payRunEntry->manualNi = $payRunEntryData->manualNi;
        $payRunEntry->nationalInsuranceCalculation = $payRunEntryData->nationalInsuranceCalculation;
        $payRunEntry->aeNotEnrolledWarning = $payRunEntryData->aeNotEnrolledWarning;
        $payRunEntry->fps = $payRunEntryData->fps;
        $payRunEntry->recievingOffsetPay = $payRunEntryData->recievingOffsetPay;
        $payRunEntry->paymentAfterLearning = $payRunEntryData->paymentAfterLearning;
        $payRunEntry->umbrellaPayment = $payRunEntryData->umbrellaPayment;
        $payRunEntry->pdf = $payRunEntryData->pdf;

        var_dump($payRunEntry->save(true));
    }
}
