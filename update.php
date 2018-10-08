<?php
/**
 *
 */

require_once 'vendor/autoload.php';

use Webmozart\Glob\Glob;
use Webmozart\PathUtil\Path;

$settings = json_decode(file_get_contents('settings.json'), true);
$dateFormat = $settings['date-format'] ?? 'd/m/Y';

$year = $settings['year'] ?? date('Y');
$csvPath = 'storage/csv/'.$year;
$jsonPath = 'storage/json/'.$year;

is_dir($csvPath) or mkdir($csvPath, 0777, true);
is_dir($jsonPath) or mkdir($jsonPath, 0777, true);

$globCsv = str_replace(
    ['${YEAR}', '~'],
    [$year, $_SERVER['HOME']],
    $settings['tournaments-path'].'/**/*-Standing.csv'
);

foreach (Glob::glob($globCsv) as $file) {
    echo " - $file\n";
    copy($file, $csvPath.'/'.basename($file));
}

$standing = [];
foreach (scandir($csvPath) as $file) {
    if ($file[0] != '.' && preg_match('/\.csv$/i', $file)) {
        $page = vegachess_get_standing_csv($csvPath . '/' . $file);
        $time = strtotime_match_format($page[0][0], $dateFormat);
        $date = date($dateFormat, $time);

        preg_match('/#([0-9]+)/', $page[0][0], $stage);

        // General standing tournaments info
        $standing['Stages'][$time] = [
            'Time' => $time,
            'Date' => $date,
            'Stage' => isset($stage[1]) ? $stage[0] : $date,
            'Rows' => [],
        ];

        for ($i = 4; $i < count($page); $i++) {
            $playerName = $page[$i][3];
            $playerTitle = $page[$i][2];
            $playerDate = $page[$i][6];
            $playerScore = $page[$i][10];
            $playerHash = md5($playerName.'|'.$playerDate);

            $row = &$standing['General']['Rows'][$playerHash];
            $row['Count']  = isset($row['Count']) ? $row['Count'] + 1 : 1;
            $row['Name']   = $playerName;
            $row['Title']  = $playerTitle;
            $row['Rating'] = 1440;
            $row[$time]    = $playerScore;
            $row['Count']  = $playerScore;
            $row['Score']  = $playerScore;
            $row['Bonus']  = $playerScore;
            $row['Total']  = $playerScore;
            $row['Trend']  = '=';

            $row = &$standing['Stages'][$time]['Rows'][$playerHash];
            $row['Name'] = $playerName;
            $row['Title'] = $playerTitle;
            $row['Score'] =  $playerScore;
            $row['Total'] = $playerScore;
        }
    }
}

// Update ranks
$rank = 1;
foreach ($standing['General']['Rows'] as &$row) {
    $row['Rank'] = $rank;
    $rank++;
}

ksort($standing['Stages']);

// Save file
file_put_contents($jsonPath.'/Standing.json', json_encode($standing, JSON_PRETTY_PRINT));
