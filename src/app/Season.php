<?php

namespace App;

class Season
{
    public static function getYearSettings($year)
    {
        $csvDirectory  = 'src/'.$year.'/csv';
        $jsonDirectory = 'storage/'.$year.'/json';

        return [
            'csv_dir' => $csvDirectory,
            'json_dir' => $jsonDirectory
        ];
    }
}
