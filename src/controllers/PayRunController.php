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
                'gross' => 2120,
                'netPay' => 1747.52,
                'totalCost' => 2359.45,
                'AEO' => 1,
                'description_AEO' => '',
                'AEOFEE' => 1,
                'description_AEOFEE' => '',
                'AHPAY' => 1,
                'description_AHPAY' => 'Accrued Holiday Pay',
                'BASIC' => 200,
                'description_BASIC' => '',
                'CISCONTROL' => '',
                'description_CISCONTROL' => '',
                'CISDEDUCTION' => '',
                'description_CISDEDUCTION' => '',
                'CISVAT' => 1,
                'description_CISVAT' => '',
                'EMPLYRNIC' =>'',
                'description_EMPLYRNIC' => '',
                'MAPS' => 1,
                'description_MAPS' => '',
                'NIC' => 1,
                'description_NIC' => '',
                'NOTUSED' => 1,
                'description_NOTUSED' => '',
                'PAYE' => 1,
                'description_PAYE' => '',
                'PAYENICC' => '',
                'description_PAYENICC' => '',
                'PBIK' => 3,
                'description_PBIK' => '',
                'PENSION' => 3,
                'description_PENSION' => '',
                'PENSIONCONTRIB' => '',
                'description_PENSIONCONTRIB' => '',
                'PENSIONCRED' =>'',
                'description_PENSIONCRED' => '',
                'PENSIONRAS' => 3,
                'description_PENSIONRAS' => 'Pension contribution (5% of £1,600.00)',
                'PENSIONSS' => 3,
                'description_PENSIONSS' => '',
                'PERCIPIO' => 110,
                'description_PERCIPIO' => 'percipio awesome sauce',
                'PGLOAN' => 3,
                'description_PGLOAN' => '',
                'SALARY' => '',
                'description_SALARY' => '',
                'SAP' => 3,
                'description_SAP' => '',
                'SHPP' => 3,
                'description_SHPP' => '',
                'SMP' => 3,
                'description_SMP' => '',
                'SPBP' => 3,
                'description_SPBP' => '',
                'SPP' => 3,
                'description_SPP' => '',
                'SSP' => 3,
                'description_SSP' => '',
                'STLOAN' => 3,
                'description_STLOAN' => 'test description',
                'TERMINATION' => 3,
                'description_TERMINATION' => ''
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

            $csvEntry = array_map(function($csv) use ($entry) { return $csv['id'] == $entry['id'] ? $csv : null; }, $entries);
            $csvEntry = $csvEntry[0] ?? [];
            $payRollCode = $csvEntry['payrollCode'] ?? null;

            unset($csvEntry['id']);
            unset($csvEntry['name']);
            unset($csvEntry['niNumber']);
            unset($csvEntry['payrollCode']);
            unset($csvEntry['gross']);
            unset($csvEntry['netPay']);
            unset($csvEntry['totalCost']);

            $payLines = PayLineRecord::find()->where(['payOptionsId' => $entry['payOptionsId'] ?? null])->all();
            $regularPayLines = [];

            foreach($payLines as $payLine) {
                $payLine = $payLine->toArray();

                //overwrite values
                $payLine['value'] = $csvEntry[$payLine['code'] ?? SecurityHelper::decrypt($payLine['value'] ?? '')];
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

                //save
                $regularPayLines[] = $payLine;
            }

            foreach(array_filter($csvEntry) as $key => $defaultPayCode){
                if(strpos($key, 'description') === false){

                    $payLine = [];
                    $payLine['code'] = $key;
                    $payLine['value'] = $defaultPayCode;

                    if($csvEntry['description_'.$key]){
                        $payLine['description'] = $csvEntry['description_'.$key];
                    }

                    $regularPayLines[] = $payLine;
                }
            }
//            $payRunEntry = PayRunEntryRecord::findOne($entry['id'] ?? null);

            $updatedEntries[] = [
                'payrollCode' => $payRollCode,
                'lines' => $regularPayLines
            ];
        }

        Staff::$plugin->payRuns->updatePayRunEntry($payPeriod, $employer, $updatedEntries);
    }
}