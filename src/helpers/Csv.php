<?php

namespace percipiolondon\staff\helpers;

use Craft;
use percipiolondon\staff\records\Employer as EmployerRecord;
use percipiolondon\staff\records\PayRunEntry as PayRunEntryRecord;
use yii\data\ArrayDataProvider;
use yii2tech\csvgrid\CsvGrid;

class Csv
{
    public static function arrayToCsv( array $fields, $filename = "payrun" )
    {
        $exporter = new CsvGrid([
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $fields
            ])
        ]);

        return $exporter->export()->send($filename.'.csv');
    }

    public static function csvArrayToPayRunEntry( array $entries )
    {
        $payRunEntry = PayRunEntryRecord::findOne($entry['id'] ?? null);

        Craft::dd($payRunEntry);
    }
}