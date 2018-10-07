<?php


/**
 * @param $file
 * @return array
 */
function vegachess_get_standing_csv($file)
{
    if (!file_exists($file)) {
        die(__FUNCTION__.": file not found.\n");
    }

    $page = [];
    $read = fopen($file, 'r');

    while (($line = fgetcsv($read, 0, ';')) !== false) {
        $page[] = $line;
    }

    fclose($read);

    return $page;
}

/**
 * @param $text
 * @param $format
 * @return false|int
 */
function strtotime_match_format($text, $format)
{
    if (strtolower($format) == 'd/m/y') {
        if (preg_match('#([0-3][0-9])/([0-1][0-9])/([0-9][0-9]+)#', $text, $data)) {
            return mktime(0, 0, 0, $data[2], $data[1], $data[3]);
        }
    }
    return time();
}
