<?php

namespace percipiolondon\staff\controllers;

use craft\web\Controller;
use percipiolondon\staff\helpers\Security as SecurityHelper;
use percipiolondon\staff\records\PayCode;
use percipiolondon\staff\records\PayLine as PayLineRecord;
use percipiolondon\staff\records\PayRunEntry as PayRunEntryRecord;
use percipiolondon\staff\Staff;

use Craft;

class PayRunController extends Controller
{
    public function actionSavePayRunEntry(int $payRunId)
    {
        //TEST --> should be in the post
        $entries = [
            [
                'id' => 36,
                'name' => ' Robert Smith',
                'niNumber' => 'JM888888A',
                'payrollCode' => '1',
                'gross' => 1650,
                'netPay' => 1427.92,
                'totalCost' => 1810.49,
                'NOTUSED' => 300,
                'description_NOTUSED' => 'testing a not used paycode',
                'PERCIPIO' => 250,
                'description_PERCIPIO' => 'percipio awesome sauce'
            ],
            [
                'id' => 38,
                'name' => 'Mr John Deo',
                'niNumber' => 'JM333333A',
                'payrollCode' => '2',
                'gross' => 2000,
                'netPay' => 1569.8,
                'totalCost' => 2320.4,
                'NOTUSED' => '',
                'description_NOTUSED' => '',
                'PERCIPIO' => 0,
                'description_PERCIPIO' => ''
            ],
            [
                'id' => 40,
                'name' => 'Mrs Jane Deo',
                'niNumber' => 'JM111111A',
                'payrollCode' => '4',
                'gross' => 1900,
                'netPay' => 1596.24,
                'totalCost' => 2101.89,
                'NOTUSED' => '',
                'description_NOTUSED' => '',
                'PERCIPIO' => '',
                'description_PERCIPIO' => 200
            ],
            [
                'id' => 42,
                'name' => 'Miss Elizabeth Jenkins',
                'niNumber' => '',
                'payrollCode' => '5',
                'gross' => 3250,
                'netPay' => 2515.44,
                'totalCost' => 3678.69,
                'NOTUSED' => 3,
                'description_NOTUSED' => 'Stefs description',
                'PERCIPIO' => 3250,
                'description_PERCIPIO' => 'percipio awesome sauce'
            ]
        ];
        //END TEST

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
}