<?php

namespace App;

use Webmozart\Glob\Glob;

class Functions
{

    /**
     * @param $csvPath
     * @return array|string
     */
    public static function scanCsv($csvPath)
    {
        $csvFiles = [];

        if (is_dir($csvPath)) {
            foreach (scandir($csvPath) as $file) {
                if ($file[0] != '.' && preg_match('/\.csv$/i', $file)) {
                    $csvFiles[] = $csvPath . '/' . $file;
                }
            }
        }

        return $csvFiles;
    }

    /**
     * @param $text
     * @param $format
     * @return false|int
     */
    public static function timeByFormat($text, $format)
    {
        if (strtolower($format) == 'd/m/y') {
            if (preg_match('#([0-3][0-9])/([0-1][0-9])/([0-9][0-9]+)#', $text, $data)) {
                return mktime(0, 0, 0, $data[2], $data[1], $data[3]);
            }
        }
        return time();
    }

}
