<?php
/**
 *
 */

require_once 'vendor/autoload.php';

use Webmozart\Glob\Glob;

$settings = circolodelre_load_settings();

$csvPath = 'storage/csv/'.$settings['year'];
$jsonPath = 'storage/json/'.$settings['year'];

is_dir($csvPath) or mkdir($csvPath, 0777, true);
is_dir($jsonPath) or mkdir($jsonPath, 0777, true);

$globCsv = str_replace(
    ['${YEAR}', '~'],
    [$settings['year'], $_SERVER['HOME']],
    $settings['tournaments-path'].'/**/*-Standing.csv'
);

foreach (Glob::glob($globCsv) as $file) {
    echo " - $file\n";
    copy($file, $csvPath.'/'.basename($file));
}

$standing = [];
$last = 0;
foreach (scandir ($csvPath) as $file) {
    if ($file[0] != '.' && preg_match('/\.csv$/i', $file)) {
        $page = vegachess_get_standing_csv($csvPath . '/' . $file);
        $time = strtotime_match_format($page[0][0], $settings['date-format']);
        $last = $time > $last ? $time : $last;
        $date = date($settings['date-format'], $time);

        preg_match('/#([0-9]+)/', $page[0][0], $stage);

        // General standing tournaments info
        $standing['stages'][$time] = [
            'time' => $time,
            'date' => $date,
            'number' => isset($stage[1]) ? $stage[0] : $date,
            'rows' => [],
        ];

        for ($i = 4; $i < count($page); $i++) {
            $player = trim($page[$i][3]);
            $title  = trim($page[$i][2]);
            $birth  = trim($page[$i][7]);
            $score  = number_format(floatval($page[$i][10]), 1);
            $buc1   = number_format(floatval($page[$i][11]), 1);
            $bucT   = number_format(floatval($page[$i][12]), 1);
            $key    = md5($player.'|'.$birth);

            $row = &$standing['general']['rows'][$key];
            $row['count']  = isset($row['count']) ? $row['count'] + 1 : 1;
            $row['player'] = $player;
            $row['title']  = $title;
            $row[$time]    = $score;
            $row['score']  = number_format(isset($row['score']) ? $row['score'] + $score : $score, 1);
            $row['bonus']  = number_format($row['count'] > 3 ? $row['count'] + 3 : $row['count'], 1);
            $row['total']  = number_format($row['score'] + $row['bonus'], 1);
            $row['rating'] = 1440 + 40 * $row['score'] - 100 * $row['count'];
            $row['trend']  = '=';

            $row = &$standing['stages'][$time]['rows'][$key];
            $row['player'] = $player;
            $row['title']  = $title;
            $row['score']  = $score;
            $row['buc1']   = $buc1;
            $row['buct']   = $bucT;
        }
    }
}

// Update ranks
usort($standing['general']['rows'], function($row0, $row1) {
    return $row0['total'] > $row1['total'] ? -1 : 1;
});

$rank = 1;
foreach ($standing['general']['rows'] as &$row) {
    $row['rank'] = $rank;
    $rank++;
}

$standing['general']['rows'] = array_values($standing['general']['rows']);


foreach ($standing['stages'] as &$stage) {
    $rank = 1;
    foreach ($stage['rows'] as &$row) {
        $row['rank'] = $rank;
        $rank++;
    }
}

ksort($standing['stages']);

// Save file
file_put_contents($jsonPath.'/Standing.json', json_encode($standing, JSON_PRETTY_PRINT));
