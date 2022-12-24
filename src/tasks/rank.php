<?php

require_once __DIR__.'/../../vendor/autoload.php';

use App\Season;
use App\Services;
use Webmozart\Glob\Glob;

echo "Build... \n";

$year = 2018;
$season = Season::getSeason($year);

echo "Syncronize year: {$year}\n";

$config = service::get('config');

$trends = [];
$championship = [
    'year' => $year
];

$standings = Standing::loadSeason($season['csv_dir'], $config['date_format']);

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

