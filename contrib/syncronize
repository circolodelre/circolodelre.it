#!/bin/env php
<?php
/**
 * Syncronize script
 *
 * this script import csv files from Vega Tournaments
 * and create updated stangings and other files.
 */

if (empty($argv[1]) || $argv[1] < 1990 || $argv[1] > 3000) {
    die("Syntax error: type a valid year number.\n");
}

require_once 'vendor/autoload.php';

use Webmozart\Glob\Glob;

$year = (int) $argv[1];

echo "Syncronize year: {$year}\n";

$settings = circolodelre_load_settings();

$csvPath  = 'storage/csv/'.$year;
$jsonPath = 'storage/json/'.$year;

$globCsv = str_replace(
    ['${YEAR}', '~'],
    [$year, $_SERVER['HOME']],
    $settings['tournaments-path'].'/**/*-Standing.csv'
);

foreach (Glob::glob($globCsv) as $file) {
    echo "Fetch standing file: {$file}\n";
    is_dir($csvPath) or mkdir($csvPath, 0777, true);
    copy($file, $csvPath.'/'.basename(dirname($file)).'-Standing.csv');
}

$globCsv = str_replace(
    ['${YEAR}', '~'],
    [$year, $_SERVER['HOME']],
    $settings['tournaments-path'].'/**/Players.csv'
);

foreach (Glob::glob($globCsv) as $file) {
    echo "Fetch players file: {$file}\n";
    is_dir($csvPath) or mkdir($csvPath, 0777, true);
    copy($file, $csvPath.'/'.basename(dirname($file)).'-Players.csv');
}

$trends = [];
$championship = [
    'year' => $year
];
$standings = circolodelre_load_standings_csv($csvPath, $settings['date-format']);

if (empty($standings)) {
    die("File error: Standing files not found.\n");
}

foreach ($standings as $time => $standing) {
    // general stages championship info
    $championship['stages'][$time] = [
        'time'   => $time,
        'date'   => $standing['date'],
        'number' => $standing['number'],
        'rows'   => [],
    ];

    // loop through standing rows
    foreach ($standing['rows'] as $row0) {
        $row = &$championship['general']['rows'][$row0['key']];
        $row['count']  = isset($row['count']) ? $row['count'] + 1 : 1;
        $row['player'] = $row0['name'];
        $row['gender'] = $row0['gender'];
        $row['title']  = $row0['title'];
        $row[$time]    = $row0['score'];
        $row['score']  = number_format(isset($row['score']) ? $row['score'] + $row0['score'] : $row0['score'], 1);
        $row['bonus']  = number_format($row['count'] > 3 ? $row['count'] + 3 : $row['count'], 1);
        $row['total']  = number_format($row['score'] + $row['bonus'], 1);

        $kappa = 38;
        $turns = 5;
        $rating = round(1440 + ($row['score'] - ($turns * $row['count'] / 2)) * $kappa);
        $row['rating-var'] = sprintf('%+d', isset($row['rating']) ? $rating - $row['rating'] : $rating - 1440);
        $row['rating'] = $rating;

        $row = &$championship['stages'][$time]['rows'][$row0['key']];
        $row['player'] = $row0['name'];
        $row['gender']  = $row0['gender'];
        $row['title']  = $row0['title'];
        $row['total']  = $row0['score'];
        $row['buc1']   = $row0['buc1'];
        $row['buct']   = $row0['buct'];

        if (!$standing['last']) {
            $row = &$trends[$row0['key']];
            $row['count']  = isset($row['count']) ? $row['count'] + 1 : 1;
            $row['score']  = number_format(isset($row['score']) ? $row['score'] + $row0['score'] : $row0['score'], 1);
            $row['bonus']  = number_format($row['count'] > 3 ? $row['count'] + 3 : $row['count'], 1);
            $row['total']  = number_format($row['score'] + $row['bonus'], 1);
        }
    }
}

// Update ranks
circolodelre_apply_rank($trends);

// Update ranks
circolodelre_apply_rank($championship['general']['rows']);

// Apply trends
foreach ($championship['general']['rows'] as $key => &$row) {
    $row['trend'] = isset($trends[$key]) ? $trends[$key]['rank'] - $row['rank'] : 0;
}

// Update ranks
foreach ($championship['stages'] as &$stage) {
    circolodelre_apply_rank($stage['rows']);
}

// Sort stages
ksort($championship['stages']);

// Save file
$championship['general']['rows'] = array_values($championship['general']['rows']);
is_dir($jsonPath) or mkdir($jsonPath, 0777, true);
$size = file_put_contents($jsonPath.'/Championship.json', json_encode($championship, JSON_PRETTY_PRINT));
echo "Championship file updated.\n";
