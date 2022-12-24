<?php
/**
 *
 *
 */



/**
 * @param $row0
 * @param $row1
 * @return int
 */
function circolodelre_standing_sort($row0, $row1)
{
    return $row0['total'] > $row1['total'] ? -1 : 1;
}

/**
 * @param $standing
 */
function circolodelre_apply_rank(&$standing)
{
    uasort($standing, 'circolodelre_standing_sort');

    $rank = 1;
    foreach ($standing as &$row) {
        $row['rank'] = $rank;
        $rank++;
    }
}

/**
 * @param $csvPath
 * @return array|string
 */
function scandir_csv($csvPath)
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
