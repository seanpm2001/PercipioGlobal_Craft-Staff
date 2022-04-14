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

use Craft;
use craft\base\Component;
use craft\base\ElementInterface;
use craft\helpers\App;
use craft\queue\QueueInterface;
use percipiolondon\staff\elements\Employer;
use GuzzleHttp\Exception\GuzzleException;
use percipiolondon\staff\elements\PayRun;
use percipiolondon\staff\elements\PayRunEntry;
use percipiolondon\staff\helpers\Csv as CsvHelper;
use percipiolondon\staff\helpers\Logger;
use percipiolondon\staff\helpers\Security as SecurityHelper;
use percipiolondon\staff\jobs\CreatePayCodeJob;
use percipiolondon\staff\jobs\CreatePayRunEntryJob;

use percipiolondon\staff\jobs\CreatePayRunJob;
use percipiolondon\staff\jobs\FetchPayCodesListJob;
use percipiolondon\staff\records\Employee as EmployeeRecord;
use percipiolondon\staff\records\Employer as EmployerRecord;
use percipiolondon\staff\records\EmploymentDetails;
use percipiolondon\staff\records\FpsFields;
use percipiolondon\staff\records\PayCode as PayCodeRecord;
use percipiolondon\staff\records\PayLine as PayLineRecord;

use percipiolondon\staff\records\PayRun as PayRunRecord;
use percipiolondon\staff\records\PayRunEntry as PayRunEntryRecord;
use percipiolondon\staff\records\PayRunLog;
use percipiolondon\staff\records\PayRunTotals;
use percipiolondon\staff\records\PensionSummary;
use percipiolondon\staff\records\PersonalDetails;
use percipiolondon\staff\records\WorkerGroup;
use percipiolondon\staff\Staff;
use yii\db\Exception;
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
    public function getLastPayRunByEmployer(int $employerId): ElementInterface|null
    {
        return PayRun::find()
            ->employerId($employerId)
            ->orderBy('dateCreated DESC')
            ->one();
    }

    public function getTotalsById(int $totalsId): array
    {
        $payRunTotals = PayRunTotals::findOne($totalsId);
        return $payRunTotals ? $payRunTotals->toArray() : [];
    }

    /**
     * @param int $payRunId
     */
    public function getCsvTemplate(int $payRunId): void
    {
        // fetch pay run
        $payRunQuery = PayRunRecord::findOne($payRunId);
        $payRunQuery = $payRunQuery ? $payRunQuery->toArray() : [];

        // fetch employer
        $employer = EmployerRecord::findOne($payRunQuery['employerId'] ?? null);
        $employer = $employer ? $employer->toArray() : [];
        $employer = Staff::$plugin->employers->parseEmployer($employer);

        $csvEntries = $this->getCsvData($payRunId);

        CsvHelper::arrayToCsv($csvEntries, 'pay-' . ($employer['slug'] ?? 'x') . '-' . ($payRunQuery['taxMonth'] ?? 'x') . '-' . strtolower($payRunQuery['taxYear']) ?? 'x');
    }

    /**
     * @param int $payRunId
     * @param bool $fetchHeaders
     * @return array
     */
    public function getCsvData(int $payRunId, bool $fetchHeaders = false): array
    {
        // fetch pay run
        $payRunQuery = PayRunRecord::findOne($payRunId);
        $payRunQuery = $payRunQuery ? $payRunQuery->toArray() : [];

        // fetch pay run entries
        $payRunId = $payRunQuery['id'] ?? null;
        $payRunEntries = $fetchHeaders ? [PayRunEntryRecord::findOne(['payRunId' => $payRunId])] : PayRunEntryRecord::findAll(['payRunId' => $payRunId]);

        // fetch employer
        $employer = EmployerRecord::findOne($payRunQuery['employerId'] ?? null);
        $employer = $employer ? $employer->toArray() : [];
        $employer = Staff::$plugin->employers->parseEmployer($employer);

        // fetch pay codes
        $payRunEmployerId = $employer['id'] ?? null;
        $payCodes = PayCodeRecord::find()->where(['employerId' => $payRunEmployerId, 'isSystemCode' => 0])->all();

        //create pay codes for all entries
        $payCodeKeys = array_map(function($code) {
            return $code['code'];
        },$payCodes);
        sort($payCodeKeys);

        $csvEntries = [];

        //fill in data
        foreach ($payRunEntries as $entry) {
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
            $totals = Staff::$plugin->totals->parseTotals($totals);

            //CSV structure
            $payRunEntry = [];
            $payRunEntry['id'] = (int)($entry['id'] ?? null);
            $payRunEntry['name'] = ($personalDetails['title'] . ' ' ?? null) . ($personalDetails['firstName'] ?? null) . ' ' . ($personalDetails['lastName'] ?? null);
            $payRunEntry['niNumber'] = $personalDetails['niNumber'] ?? null;
            $payRunEntry['payrollCode'] = $employmentDetails['payrollCode'] ?? null;
            $payRunEntry['gross'] = (float)($totals['gross'] ?? 0);
            $payRunEntry['netPay'] = (float)($totals['netPay'] ?? 0);
            $payRunEntry['totalCost'] = (float)($totals['totalCost'] ?? 0);

            //set all the pay run codes dynamic to payRunEntry with default value
            foreach ($payCodeKeys as $payCodeKey) {
                $payRunEntry[$payCodeKey] = '';
                $payRunEntry['description_' . $payCodeKey] = '';
            }

            //overwrite custom pay lines
            foreach ($payLines as $payLine) {
                $payLine = $payLine->toArray();
                $payLine = $this->_parsePayLines($payLine);

                if ($payLine && in_array($payLine['code'], $payCodeKeys, true)) {
                    $payRunEntry[$payLine['code']] = (float)($payLine['value'] ?? '');
                    $payRunEntry['description_' . $payLine['code']] = $payLine['description'] ?? '';
                }
            }

            $csvEntries[] = $payRunEntry;
        }

        usort($csvEntries, function($a, $b) {
            return $a['payrollCode'] > $b['payrollCode'];
        });

        return $csvEntries;
    }


    /* SAVES */


    /**
     * @param array $payCodes
     * @param array $employer
     */
    public function fetchPayCodes(array $payCodes, array $employer): void
    {
        $queue = Craft::$app->getQueue();
        $queue->push(new CreatePayCodeJob([
            'description' => 'Save pay codes',
            'criteria' => [
                'payCodes' => $payCodes,
                'employer' => $employer,
            ],
        ]));
    }

    /**
     * @param array $employer
     */
    public function fetchPayCodesList(array $employer): void
    {
        $queue = Craft::$app->getQueue();
        $queue->push(new FetchPayCodesListJob([
            'description' => 'Fetch pay codes',
            'criteria' => [
                'employer' => $employer,
            ],
        ]));
    }

    /**
     * @param array $employer
     * @param bool $startQueue
     */
    public function fetchPayRunByStaffologyEmployer(array $employer, bool $startQueue = false): void
    {
        $payRuns = $employer['metadata']['payruns'] ?? [];

        $queue = Craft::$app->getQueue();
        $queue->push(new CreatePayRunJob([
            'description' => 'Fetch pay runs',
            'criteria' => [
                'payRuns' => $payRuns,
                'employer' => $employer,
            ],
        ]));

        if ($startQueue) {
            $queue = Craft::$app->getQueue();
            if ($queue instanceof QueueInterface) {
                $queue->run();
            } elseif ($queue instanceof RedisQueue) {
                $queue->run(false);
            }
        }
    }

    /**
     * @param int $employerId
     * @param string $taxYear
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function fetchPayRunByEmployer(int $employerId, string $taxYear = '')
    {
        $logger = new Logger();

        if (!$employerId) {
            $logger->stdout("There's no employer id provided" . PHP_EOL, $logger::FG_RED);
            Craft::error("There's no employer id provided", __METHOD__);
        }

        $employer = Staff::$plugin->employers->getEmployerById($employerId);

        if ($employer) {
            $id = $employer['staffologyId'] ?? '';
            $taxYear = $taxYear === '' ? $employer['currentYear'] : $taxYear;
            $payPeriod = $employer['defaultPayOptions']['period'] ?? 'Monthly';

            $url = '/employers/' . $id . '/payrun/' . $taxYear . '/' . $payPeriod;

            $api = App::parseEnv(Staff::$plugin->getSettings()->apiKeyStaffology);
            $credentials = base64_encode('staff:' . $api);
            $headers = [
                'headers' => [
                    'Authorization' => 'Basic ' . $credentials,
                ],
            ];
            $client = new \GuzzleHttp\Client();

            try {
                $response = $client->get("https://api.staffology.co.uk/" . $url, $headers);
                $payRunData = json_decode($response->getBody()->getContents(), true);

                if ($payRunData) {
                    $employer['id'] = $employer['staffologyId'];

                    $this->fetchPayCodesList($employer);
                    $this->fetchPayRuns($payRunData, $employer);

                    App::maxPowerCaptain();
                    $queue = Craft::$app->getQueue();
                    if ($queue instanceof QueueInterface) {
                        $queue->run();
                    } elseif ($queue instanceof RedisQueue) {
                        $queue->run(false);
                    }
                }
            } catch (\Exception $e) {
                $logger->stdout(PHP_EOL, $logger::RESET);
                $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
                Craft::error($e->getMessage(), __METHOD__);
            }
        }
    }

    /**
     * @param array $payRuns
     * @param array $employer
     */
    public function fetchPayRuns(array $payRuns, array $employer): void
    {
        $queue = Craft::$app->getQueue();
        $queue->push(new CreatePayRunJob([
            'description' => 'Fetch pay runs',
            'criteria' => [
                'payRuns' => $payRuns,
                'employer' => $employer,
            ],
        ]));
    }

    /**
     * @param int $payRunId
     * @param bool $startQueue
     */
    public function fetchPayRunByPayRunId(int $payRunId, bool $startQueue = false): void
    {
        $payRun = PayRunRecord::findOne($payRunId);

        $employerId = $payRun['employerId'] ?? null;
        $employer = EmployerRecord::findOne($employerId);

        if ($payRun && $employer) {
            $queue = Craft::$app->getQueue();
            $queue->push(new CreatePayRunJob([
                'description' => 'Fetch pay runs',
                'criteria' => [
                    'payRuns' => [$payRun],
                    'employer' => $employer->toArray(),
                ],
            ]));

            if ($startQueue) {
                $queue = Craft::$app->getQueue();
                if ($queue instanceof QueueInterface) {
                    $queue->run();
                } elseif ($queue instanceof RedisQueue) {
                    $queue->run(false);
                }
            }
        }
    }

    /**
     * @param array $payCode
     * @param array $employer
     */
    public function savePayCode(array $payCode, array $employer): void
    {
        $logger = new Logger();
        $logger->stdout("✓ Save pay code " . $payCode['code'] . "...", $logger::RESET);

        $employerRecord = is_int($employer['id'] ?? null) ? $employer : EmployerRecord::findOne(['staffologyId' => $employer['id'] ?? null]);
        $payCodeRecord = PayCodeRecord::findOne(['code' => $payCode['code'], 'employerId' => $employerRecord->id ?? null]);

        if (!$payCodeRecord) {
            $payCodeRecord = new PayCodeRecord();
        }

        $payCodeRecord->title = $payCode['title'] ?? null;
        $payCodeRecord->code = $payCode['code'] ?? null;
        $payCodeRecord->employerId = $employerRecord->id ?? null;
        $payCodeRecord->defaultValue = SecurityHelper::encrypt($payCode['defaultValue'] ?? '');
        $payCodeRecord->isSystemCode = $payCode['isSystemCode'] ?? null;
        $success = $payCodeRecord->save();

        if ($success) {
            $logger->stdout(" done" . PHP_EOL, $logger::FG_GREEN);
        } else {
            $logger->stdout(" failed" . PHP_EOL, $logger::FG_RED);

            $errors = "";

            foreach ($payCodeRecord->errors as $err) {
                $errors .= implode(',', $err);
            }

            $logger->stdout($errors . PHP_EOL, $logger::FG_RED);
            Craft::error($payCodeRecord->errors, __METHOD__);
        }
    }

    /**
     * @param array $payRun
     * @param string $payRunUrl
     * @param array $employer
     * @throws \Throwable
     */
    public function savePayRun(array $payRun, string $payRunUrl, array $employer): void
    {
        $logger = new Logger();
        $logger->stdout("✓ Save pay run of " . $employer['name'] . ' ' . $payRun['taxYear'] . ' / ' . $payRun['taxMonth'] . '...', $logger::RESET);

        $payRunRecord = PayRun::findOne(['url' => $payRunUrl]);

        try {
            if (!$payRunRecord) {
                $payRunRecord = new PayRun();
            }

            //foreign keys
//            $totals = Staff::$plugin->totals->saveTotals($payRun['totals'] ?? [], $totalsId);
            $emp = is_int($employer['id'] ?? null) ? $employer : EmployerRecord::findOne(['staffologyId' => $employer['id'] ?? null]);

            $payRunRecord->employerId = $emp->id ?? null;
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
            $payRunRecord->state = $payRun['state'] ?? '';
            $payRunRecord->isClosed = $payRun['isClosed'] ?? '';
            $payRunRecord->dateClosed = $payRun['dateClosed'] ?? null;
            $payRunRecord->url = $payRunUrl ?? '';

            $elementsService = Craft::$app->getElements();
            $success = $elementsService->saveElement($payRunRecord);

            if ($success) {

                // GET PAYRUNENTRY FROM PAYRUN
                $queue = Craft::$app->getQueue();
                $queue->push(new CreatePayRunEntryJob([
                    'description' => 'Fetch pay run entry',
                    'criteria' => [
                        'payRun' => $payRunRecord,
                        'payRunEntries' => $payRun['entries'],
                        'employer' => $employer,
                    ],
                ]));

                $logger->stdout(" done" . PHP_EOL, $logger::FG_GREEN);

                if ($payRun['totals'] ?? null) {
                    Staff::$plugin->totals->savePayRunTotals($payRun['totals'], $payRunRecord->id);
                }
//                $this->savePayRunLog($payRun, $payRunUrl, $payRunRecord->id, $employer['id']);
            } else {
                $logger->stdout(" failed" . PHP_EOL, $logger::FG_RED);

                $errors = "";

                foreach ($payRunRecord->errors as $err) {
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

    /**
     * @param array $payRun
     * @param string $url
     * @param string $payRunId
     * @param string $employerId
     * @return bool
     */
    public function savePayRunLog(array $payRun, string $url, string $payRunId, string $employerId): bool
    {
        $logger = new Logger();
        $logger->stdout('✓ Save pay run log ...', $logger::RESET);

        $payRunLog = new PayRunLog();
        $employer = is_int($employerId ?? null) ? EmployerRecord::findOne($employerId) : EmployerRecord::findOne(['staffologyId' => $employerId ?? null]);

        $payRunLog->employerId = $employer->id ?? null;

        $payRunLog->taxYear = $payRun['taxYear'] ?? '';
        $payRunLog->employeeCount = $payRun['employeeCount'] ?? null;
        $payRunLog->lastPeriodNumber = $payRun['employeeCount'] ?? null;
        $payRunLog->url = $url ?? '';
        $payRunLog->payRunId = $payRunId;

        $success = $payRunLog->save(true);

        if ($success) {
            $logger->stdout(" done" . PHP_EOL, $logger::FG_GREEN);
        } else {
            $logger->stdout(" failed" . PHP_EOL, $logger::FG_RED);

            $errors = "";

            foreach ($payRunLog->errors as $err) {
                $errors .= implode(',', $err);
            }

            $logger->stdout($errors . PHP_EOL, $logger::FG_RED);
            Craft::error($payRunLog->errors, __METHOD__);
        }

        return $success;
    }
}
