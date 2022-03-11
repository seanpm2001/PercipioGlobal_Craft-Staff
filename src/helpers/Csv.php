<?php

namespace percipiolondon\staff\helpers;

use Craft;
use percipiolondon\staff\records\Employer as EmployerRecord;
use percipiolondon\staff\records\PayRunEntry as PayRunEntryRecord;

class Csv
{
    public static function arrayToCsv( array $fields, $filename = "payrun.csv" )
    {
        header( 'Content-Type: text/csv' );
        header( 'Content-Disposition: attachment;filename='.$filename);
        $fp = fopen('php://output', 'w');

        foreach ( $fields as $field ) {
            fputcsv($fp, array_keys($field));
            fputcsv($fp, array_values($field));
        }

        fclose($fp);
    }

    public static function csvArrayToPayRunEntry( array $entries )
    {
        $payRunEntry = PayRunEntryRecord::findOne($entry['id'] ?? null);

        Craft::dd($payRunEntry);
    }
}