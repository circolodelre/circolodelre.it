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
    $csvFiles = scandir_csv($csvPath);
    $standings = [];

    foreach ($csvFiles as $file) {
        if (preg_match('/^(.*)-Standing\\.csv$/', $file, $name)) {
            $players = vegachess_get_players_csv($name[1].'-Players.csv');
            $standing = vegachess_get_standing_csv($file);
            foreach ($standing['rows'] as &$row) {
                $row['gender'] = $players['rows'][$row['id']]['gender'];
            }
            $time = strtotime_match_format($standing['name'], $dateFormat);
            preg_match('/#([0-9]+)/', $standing['name'], $number);
            $standing['last'] = false;
            $standing['date'] = date($dateFormat, $time);
            $standing['number'] = isset($number[1]) ? $number[0] : $standing['date'];
            $standings[$time] = $standing;
            $last = $time > $last ? $time : $last;
        }
    }

    $standings[$last]['last'] = true;

    ksort($standings);

    return $standings;
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
        $field = str_replace(' ', '-', strtolower(trim($field)));
    }

    for ($i = 4; $i < count($page); $i++) {
        $row = [];

        foreach ($page[3] as $c => $field) {
            $row[$field] = trim($page[$i][$c]);
        }

        $row['key'] = md5($row['name'].'|'.$row['birth']);
        $standing['rows'][$row['key']] = $row;
    }

    return $standing;
}

/**
 * @param $file
 * @return array
 * @throws Exception
 */
function vegachess_get_players_csv($file)
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

    $players = [
        'name'   => trim($page[0][0]),
        'rows'   => [],
    ];

    foreach ($page[2] as &$field) {
        $field = str_replace(' ', '-', strtolower(trim($field)));
    }

    for ($i = 3; $i < count($page); $i++) {
        $row = [];

        foreach ($page[2] as $c => $field) {
            $row[$field] = trim($page[$i][$c]);
        }

        $players['rows'][$row['n']] = $row;
    }

    return $players;
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
