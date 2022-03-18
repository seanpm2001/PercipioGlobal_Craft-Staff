<?php

namespace percipiolondon\staff\controllers;

use craft\helpers\ArrayHelper;
use craft\helpers\Queue;
use craft\web\Controller;
use League\Csv\AbstractCsv;
use League\Csv\Exception;
use League\Csv\Reader;
use League\Csv\Statement;
use percipiolondon\staff\elements\Employer;
use percipiolondon\staff\elements\PayRun;
use percipiolondon\staff\helpers\Security as SecurityHelper;
use percipiolondon\staff\records\Employer as EmployerRecord;
use percipiolondon\staff\records\PayCode;
use percipiolondon\staff\records\PayLine as PayLineRecord;
use percipiolondon\staff\records\PayRun as PayRunRecord;
use percipiolondon\staff\records\PayRunEntry as PayRunEntryRecord;
use percipiolondon\staff\Staff;

use Craft;
use yii\base\BaseObject;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

class PayRunController extends Controller
{

    /**
     * Payrun display
     *
     * @param string|null $siteHandle
     *
     * @return Response The rendered result
     * @throws NotFoundHttpException
     * @throws \yii\web\ForbiddenHttpException
     */
    public function actionIndex(): Response
    {
        $this->requireLogin();

        $variables = [];

        $pluginName = Staff::$settings->pluginName;
        $templateTitle = Craft::t('staff-management', 'Pay Runs');

        $variables['controllerHandle'] = 'payruns';
        $variables['pluginName'] = Staff::$settings->pluginName;
        $variables['title'] = $templateTitle;
        $variables['docTitle'] = "{$pluginName} - {$templateTitle}";
        $variables['selectedSubnavItem'] = 'payRuns';

        $variables['csrf'] = [
            'name' => Craft::$app->getConfig()->getGeneral()->csrfTokenName,
            'value' => Craft::$app->getRequest()->getCsrfToken(),
        ];

        // Render the template
        return $this->renderTemplate('staff-management/payruns/index', $variables);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionPayRunByEmployer(int $employerId): Response
    {
        $this->requireLogin();

        $variables = [];

        $employer = Employer::findOne($employerId);

        if(!$employer){
            throw new NotFoundHttpException();
        }

//        $payRuns = PayRunRecord::findAll(['employerId' => $employer['id']]);
        $employerName = SecurityHelper::decrypt($employer['name']) ?? '';

        $pluginName = Staff::$settings->pluginName;
        $templateTitle = Craft::t('staff-management', 'Pay Runs > '.$employerName);

        $variables['controllerHandle'] = 'payruns';
        $variables['pluginName'] = Staff::$settings->pluginName;
        $variables['title'] = $templateTitle;
        $variables['docTitle'] = "{$pluginName} - {$templateTitle} - {$employerName}";
        $variables['selectedSubnavItem'] = 'payRuns';

        $variables['employerId'] = $employer['id'];

        $variables['csrf'] = [
            'name' => Craft::$app->getConfig()->getGeneral()->csrfTokenName,
            'value' => Craft::$app->getRequest()->getCsrfToken(),
        ];

        // Render the template
        return $this->renderTemplate('staff-management/payruns/employer', $variables);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionDetail(int $employerId, int $payRunId): Response
    {
        $this->requireLogin();

        $variables = [];

        $employer = Employer::findOne($employerId);
        $payRun = PayRun::findOne($payRunId);

        if(!$employer || !$payRun){
            throw new NotFoundHttpException();
        }

//        $payRuns = PayRunRecord::findAll(['employerId' => $employer['id']]);
        $employerName = SecurityHelper::decrypt($employer['name']) ?? '';
        $taxYear = $payRun['taxYear'] ?? '';
        $period = $payRun['period'] ?? '';
        
        $pluginName = Staff::$settings->pluginName;
        $templateTitle = Craft::t('staff-management', 'Pay Runs > '.$employerName. ' > '.$taxYear.' / '.$period);

        $variables['controllerHandle'] = 'payruns';
        $variables['pluginName'] = Staff::$settings->pluginName;
        $variables['title'] = $templateTitle;
        $variables['docTitle'] = "{$pluginName} - {$templateTitle} - {$employerName}";
        $variables['selectedSubnavItem'] = 'payRuns';

        $variables['employerId'] = $employer['id'];

        $variables['csrf'] = [
            'name' => Craft::$app->getConfig()->getGeneral()->csrfTokenName,
            'value' => Craft::$app->getRequest()->getCsrfToken(),
        ];

        // Render the template
        return $this->renderTemplate('staff-management/payruns/detail', $variables);
    }

    public function actionDownloadTemplate(int $payRunId): void
    {
        $payRun = PayRun::findOne($payRunId);

        if(!$payRun){
            throw new NotFoundHttpException();
        }

        Staff::$plugin->payRuns->getCsvTemplate($payRunId);
    }

    public function actionImport()
    {
        $this->requireLogin();
        $this->requirePostRequest();

        $request = Craft::$app->getRequest();
        $payRunId = $request->getBodyParam('payRunId');
        $employerId = $request->getBodyParam('employerId');

        $employer = Employer::findOne($employerId);
        $payRun = PayRun::findOne($payRunId);

        if (!$employer || !$payRun) {
            throw new NotFoundHttpException();
        }

        //SAVE FILE
        $filename = "";
        $headers = null;
        $filePath = "";

        $file = UploadedFile::getInstanceByName('file');

        if ($file !== null) {
            $filename = uniqid($file->name, true);
            $filePath = Craft::$app->getPath()->getTempPath().DIRECTORY_SEPARATOR.$filename;
            $file->saveAs($filePath, false);
            // Also save the file to the cache as a backup way to access it
            $cache = Craft::$app->getCache();
            $fileHandle = fopen($filePath, 'r');
            if ($fileHandle) {
                $fileContents = fgets($fileHandle);
                if ($fileContents) {
                    $cache->set($filePath, $fileContents);
                }
                fclose($fileHandle);
            }
            // Read in the headers
            $csv = Reader::createFromPath($file->tempName);
            try {
                $csv->setDelimiter(';');
            } catch (Exception $e) {
                Craft::error($e, __METHOD__);
            }
            $headers = $csv->fetchOne(0);
        }

        //PARSE CSV
        try {
            $csv = Reader::createFromPath($filePath);
            $csv->setDelimiter(';');
            $headers = array_flip($csv->fetchOne(0));
        } catch (\Exception $e) {
            // If this throws an exception, try to read the CSV file from the data cache
            // This can happen on load balancer setups where the Craft temp directory isn't shared
            $cache = Craft::$app->getCache();
            $cachedFile = $cache->get($filename);
            if ($cachedFile !== false) {
                $csv = Reader::createFromString($cachedFile);
                try {
                    $csv->setDelimiter(',');
                } catch (Exception $e) {
                    Craft::error($e, __METHOD__);
                }
                $headers = array_flip($csv->fetchOne(0));
                $cache->delete($filename);
            } else {
                Craft::error("Could not import ${$filename} from the file system, or the cache.", __METHOD__);
            }
        }

        // If we have headers, then we have a file, so parse it
        if ($headers !== null) {
            $entries = $this->importCsvApi9($csv, $headers, $payRunId);
            $this->saveEntriesToStaffology($payRunId, $entries);
            @unlink($filename);
            Craft::$app->getSession()->setNotice(Craft::t('staff-management', 'Imports from CSV started.'));
        } else {
            Craft::$app->getSession()->setError(Craft::t('staff-management', 'CSV imports could not be imported.'));
        }
    }


    protected function saveEntriesToStaffology(int $payRunId, array $entries)
    {
        $savedEntries = Staff::$plugin->payRuns->setPayRunEntry($entries);

        $updatedEntries = [];
        $payPeriod = null;
        $employer = null;

        foreach($savedEntries as $entry) {

            $entry = $entry->toArray();

            $payPeriod = $entry['payPeriod'] ?? null;
            $employer = $entry['employerId'] ?? null;

            $csvEntry = array_filter($entries, function($csv) use ($entry){  return $csv['id'] == $entry['id']; });

            $csvEntry = reset($csvEntry) ?? [];
            $payRollCode = $csvEntry['payrollCode'] ?? null;

            $payLines = PayLineRecord::find()->where(['payOptionsId' => $entry['payOptionsId'] ?? null])->all();
            $regularPayLines = [];

            unset($csvEntry['id']);
            unset($csvEntry['name']);
            unset($csvEntry['niNumber']);
            unset($csvEntry['payrollCode']);
            unset($csvEntry['gross']);
            unset($csvEntry['netPay']);
            unset($csvEntry['totalCost']);

            foreach($payLines as $payLine) {
                $payLine = $payLine->toArray();
                $code = $payLine['code'] ?? SecurityHelper::decrypt($payLine['value'] ?? '');

                if($payLine && array_key_exists($code, $csvEntry)){
                    $value = $csvEntry[$payLine['code'] ?? SecurityHelper::decrypt($payLine['value'] ?? '')];

                    //overwrite values
                    $payLine['value'] = $value;
                    $payLine['rate'] = SecurityHelper::decrypt($payLine['rate'] ?? '');
                    $payLine['description'] = $csvEntry['description_'.$payLine['code'] ?? $payLine['description'] ?? ''];

                    //reset in csvEntry to check which ones are new to save later on
                    $csvEntry[$payLine['code']] = '';

                    //remove own fields
                    unset($payLine['id']);
                    unset($payLine['dateCreated']);
                    unset($payLine['dateUpdated']);
                    unset($payLine['uid']);
                    unset($payLine['payOptionsId']);

                    if($payLine['value'] != ''){
                        //save
                        $regularPayLines[] = $payLine;
                    }
                }
            }

            foreach(array_filter($csvEntry) as $key => $defaultPayCode) {

                if(strpos($key, 'description') === false && strlen($defaultPayCode) > 0){

                    $payLine = [];
                    $payLine['code'] = $key;
                    $payLine['value'] = $defaultPayCode;

                    if(array_key_exists('description_'.$key, $csvEntry)){
                        $payLine['description'] = $csvEntry['description_'.$key];
                    }

                    $regularPayLines[] = $payLine;
                }
            }

            $updatedEntries[] = [
                'payrollCode' => $payRollCode,
                'lines' => $regularPayLines
            ];
        }

        Staff::$plugin->payRuns->updatePayRunEntry($payPeriod, $employer, $payRunId, $updatedEntries);
    }

    /**
     * @param AbstractCsv $csv
     * @param array $columns
     * @param array $headers
     * @throws \League\Csv\Exception
     */
    protected function importCsvApi9(AbstractCsv $csv, array $headers, string $payRunId): array
    {
        $csvEntries = Staff::$plugin->payRuns->getCsvData($payRunId, true);

        if(count($csvEntries) > 0){
            $columns = array_keys($csvEntries[0]);

            $stmt = (new Statement())
                ->offset(1)
            ;

            $rows = $stmt->process($csv);
            $columns = ArrayHelper::filterEmptyStringsFromArray($columns);
            $totalRows = count($csv) - 1;
            $rowCount = 0;

            $entries = [];

            foreach ($rows as $row) {
                $rowCount++;

                $entry = [];

                $index = 0;
                foreach ($columns as $importField) {

                    if (isset($columns[$index], $headers[$columns[$index]])) {
                        $entry[$importField] = empty($row[$headers[$columns[$index]]])
                            ? null
                            : $row[$headers[$columns[$index]]];
                    }
                    $index++;
                }

                $entries[] = $entry;
            }

            return $entries;

        }

        return [];

    }
}

//        //TEST --> should be in the post
//        $entries = [
//            [
//                'id' => 36,
//                'name' => ' Robert Smith',
//                'niNumber' => 'JM888888A',
//                'payrollCode' => '1',
//                'gross' => 1650,
//                'netPay' => 1427.92,
//                'totalCost' => 1810.49,
//                'NOTUSED' => 300,
//                'description_NOTUSED' => 'testing a not used paycode',
//                'PERCIPIO' => 250,
//                'description_PERCIPIO' => 'percipio awesome sauce'
//            ],
//            [
//                'id' => 38,
//                'name' => 'Mr John Deo',
//                'niNumber' => 'JM333333A',
//                'payrollCode' => '2',
//                'gross' => 2000,
//                'netPay' => 1569.8,
//                'totalCost' => 2320.4,
//                'NOTUSED' => '',
//                'description_NOTUSED' => '',
//                'PERCIPIO' => 0,
//                'description_PERCIPIO' => ''
//            ],
//            [
//                'id' => 40,
//                'name' => 'Mrs Jane Deo',
//                'niNumber' => 'JM111111A',
//                'payrollCode' => '4',
//                'gross' => 1900,
//                'netPay' => 1596.24,
//                'totalCost' => 2101.89,
//                'NOTUSED' => '',
//                'description_NOTUSED' => '',
//                'PERCIPIO' => '',
//                'description_PERCIPIO' => 200
//            ],
//            [
//                'id' => 42,
//                'name' => 'Miss Elizabeth Jenkins',
//                'niNumber' => '',
//                'payrollCode' => '5',
//                'gross' => 3250,
//                'netPay' => 2515.44,
//                'totalCost' => 3678.69,
//                'NOTUSED' => 3,
//                'description_NOTUSED' => 'Stefs description',
//                'PERCIPIO' => 3250,
//                'description_PERCIPIO' => 'percipio awesome sauce'
//            ]
//        ];
//        //END TEST
//
//        $savedEntries = Staff::$plugin->payRuns->setPayRunEntry($entries);
//        $updatedEntries = [];
//        $payPeriod = null;
//        $employer = null;
//
//        foreach($savedEntries as $entry) {
//
//            $entry = $entry->toArray();
//
//            $payPeriod = $entry['payPeriod'] ?? null;
//            $employer = $entry['employerId'] ?? null;
//
//            $csvEntry = array_filter($entries, function($csv) use ($entry){  return $csv['id'] == $entry['id']; });
//
//            $csvEntry = reset($csvEntry) ?? [];
//            $payRollCode = $csvEntry['payrollCode'] ?? null;
//
//            $payLines = PayLineRecord::find()->where(['payOptionsId' => $entry['payOptionsId'] ?? null])->all();
//            $regularPayLines = [];
//
//            unset($csvEntry['id']);
//            unset($csvEntry['name']);
//            unset($csvEntry['niNumber']);
//            unset($csvEntry['payrollCode']);
//            unset($csvEntry['gross']);
//            unset($csvEntry['netPay']);
//            unset($csvEntry['totalCost']);
//
//            foreach($payLines as $payLine) {
//                $payLine = $payLine->toArray();
//                $code = $payLine['code'] ?? SecurityHelper::decrypt($payLine['value'] ?? '');
//
//                if($payLine && array_key_exists($code, $csvEntry)){
//                    $value = $csvEntry[$payLine['code'] ?? SecurityHelper::decrypt($payLine['value'] ?? '')];
//
//                    //overwrite values
//                    $payLine['value'] = $value;
//                    $payLine['rate'] = SecurityHelper::decrypt($payLine['rate'] ?? '');
//                    $payLine['description'] = $csvEntry['description_'.$payLine['code'] ?? $payLine['description'] ?? ''];
//
//                    //reset in csvEntry to check which ones are new to save later on
//                    $csvEntry[$payLine['code']] = '';
//
//                    //remove own fields
//                    unset($payLine['id']);
//                    unset($payLine['dateCreated']);
//                    unset($payLine['dateUpdated']);
//                    unset($payLine['uid']);
//                    unset($payLine['payOptionsId']);
//
//                    if($payLine['value'] != ''){
//                        //save
//                        $regularPayLines[] = $payLine;
//                    }
//                }
//            }
//
//            foreach(array_filter($csvEntry) as $key => $defaultPayCode) {
//
//                if(strpos($key, 'description') === false && strlen($defaultPayCode) > 0){
//
//                    $payLine = [];
//                    $payLine['code'] = $key;
//                    $payLine['value'] = $defaultPayCode;
//
//                    if(array_key_exists('description_'.$key, $csvEntry)){
//                        $payLine['description'] = $csvEntry['description_'.$key];
//                    }
//
//                    $regularPayLines[] = $payLine;
//                }
//            }
//
//            $updatedEntries[] = [
//                'payrollCode' => $payRollCode,
//                'lines' => $regularPayLines
//            ];
//        }