<?php

namespace percipiolondon\staff\controllers;

use craft\web\Controller;
use percipiolondon\staff\records\PayRunEntry as PayRunEntryRecord;
use percipiolondon\staff\Staff;

use Craft;

class PayRunController extends Controller
{
    public function actionSavePayRunEntry(int $payRunId)
    {
        //TEST --> should be in the post
        $entries = Staff::$plugin->payRuns->getCsvTemplate($payRunId);
        //END TEST

        Staff::$plugin->payRuns->setPayRunEntry($entries);

//        foreach($entries as $entry) {
//            Craft::dd($entry);
//            $payRunEntry = PayRunEntryRecord::findOne($entry['id'] ?? null);
//            Craft::dd($payRunEntry);
//        }

//        CsvHelper::arrayToCsv($entries);
    }
}