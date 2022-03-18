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
//        'columns' => [
//        ['attribute' => 'id'],
//        ['attribute' => 'name'],
//        ['attribute' => 'niNumber'],
//        ['attribute' => 'payrollCode'],
//        ['attribute' => 'gross'],
//        ['attribute' => 'netPay'],
//        ['attribute' => 'totalCost'],
//        ['attribute' => ''],
//        ['attribute' => 'email'],
//        ['attribute' => 'jobRole'],
//        ['attribute' => 'modulesAttended'],
//        ['attribute' => 'mailingList'],
//        ['attribute' => 'anonymous'],
//    ],

        $exporter = new CsvGrid([
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $fields
            ])
        ]);

        return $exporter->export()->send($filename.'.csv');
    }

//    public static function arrayToCsv( array $fields, $filename = "payrun" )
//    {
//        header( 'Content-Disposition: attachment;filename='.$filename.'.csv');
//
//        $list = array (
//            array('aaa', 'bbb', 'ccc', 'dddd'),
//            array('123', '456', '789'),
//            array('"aaa"', '"bbb"')
//        );
//
//        $fp = fopen('php://output', 'w');
//
//        foreach ($list as $fields) {
//            fputcsv($fp, $fields);
//        }
//
//        fclose($fp);
//
//////        header( 'Content-Type: text/csv' );
//////        header( 'Content-Disposition: attachment;filename='.$filename.'.csv');
////        $fp = fopen('php://output', 'w');
////
////        $loop = 0;
////
////        foreach ( $fields as $index => $field ) {
////
////            if($loop === 0){
////                fputcsv($fp, array_keys($field));
////            }
////
////            fputcsv($fp, array_values($field));
////
////            $loop++;
////        }
////
////        fclose($fp);
//    }

    public static function csvArrayToPayRunEntry( array $entries )
    {
        $payRunEntry = PayRunEntryRecord::findOne($entry['id'] ?? null);

        Craft::dd($payRunEntry);
    }
}