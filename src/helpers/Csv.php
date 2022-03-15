<?php

namespace percipiolondon\staff\helpers;

use Craft;
use percipiolondon\staff\records\Employer as EmployerRecord;
use percipiolondon\staff\records\PayRunEntry as PayRunEntryRecord;

class Csv
{
    public static function arrayToCsv( array $fields, $filename = "payrun" )
    {
        header( 'Content-Type: text/csv' );
        header( 'Content-Disposition: attachment;filename='.$filename.'.csv');
        $fp = fopen('php://output', 'w');

        $loop = 0;

        foreach ( $fields as $index => $field ) {

            if($loop === 0){
                fputcsv($fp, array_keys($field));
            }

            fputcsv($fp, array_values($field));


            $loop++;
        }

        fclose($fp);
    }

    public static function csvArrayToPayRunEntry( array $entries )
    {
        $payRunEntry = PayRunEntryRecord::findOne($entry['id'] ?? null);

        Craft::dd($payRunEntry);
    }
}