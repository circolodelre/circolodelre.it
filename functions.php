<?php

/**
 *
 */
function circolodelre_load_settings()
{
    $settings = json_decode(file_get_contents('settings.json'), true);

    $settings['date-format'] = $settings['date-format'] ?? 'd/m/Y';
    $settings['current-year'] = $settings['current-year'] ?? date('Y');

    return $settings;
}

/**
 * @param $csvPath
 * @param $dateFormat
 * @return array
 */
function circolodelre_load_standings_csv($csvPath, $dateFormat)
{
    $last = 0;
    $pages = [];
    $csvFiles = scandir_csv($csvPath);

    foreach ($csvFiles as $file) {
        $standing = vegachess_get_standing_csv($file);
        $time = strtotime_match_format($standing['name'], $dateFormat);
        preg_match('/#([0-9]+)/', $standing['name'], $number);
        $page['last'] = false;
        $page['date'] = date($dateFormat, $time);
        $page['number'] = isset($number[1]) ? $number[0] : $page['date'];
        $pages[$time] = $page;
        $last = $time > $last ? $time : $last;
    }

    $pages[$last]['last'] = true;

    return $pages;
}

/**
 * @param $value1
 * @param $value2
 * @return string
 */
function circolodelre_trend_sign($value1, $value2)
{
    if ($value1 > $value2) {
        return '<';
    } elseif ($value1 < $value2) {
        return '>';
    } else {
        return '=';
    }
}

/**
 * @param $csvPath
 * @return array|string
 */
function scandir_csv($csvPath)
{
    $csvFiles = [];

    foreach (scandir ($csvPath) as $file) {
        if ($file[0] != '.' && preg_match('/\.csv$/i', $file)) {
            $csvFiles[] = $csvPath . '/' . $file;
        }
    }

    return $csvFiles;
}


/**
 * @param $file
 * @return array
 * @throws Exception
 */
function vegachess_get_standing_csv($file)
{
    if (!file_exists($file)) {
        throw new Exception("file not found.\n");
    }

    $page = [];
    $read = fopen($file, 'r');

    while (($line = fgetcsv($read, 0, ';')) !== false) {
        $page[] = $line;
    }

    fclose($read);

    $standing = [
        'name'   => trim($page[0][0]),
        'rows'   => [],
    ];

    foreach ($page[3] as &$field) {
        $field = strtolower(trim($file));
    }

    for ($i = 4; $i < count($page); $i++) {
        $row = [];
        foreach ($page[3] as $c => $field) {
            $row[$field] = $page[$i][$c];
        }
        $row['key'] = md5($row['name'].'|'.$row['birth']);
        $standing['rows'][] = $row;
    }

    return $standing;
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
