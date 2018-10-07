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
    copy($file, $jsonPath.'/'.basename($file));
}


//switch ($settings['date-format'])

$standing = [
    0 => []
];

foreach (scandir($csvPath) as $file) {
    if ($file[0] != '.' && preg_match('/\.csv$/i', $file)) {
        $page = vegachess_get_standing_csv($csvPath . '/' . $file);
        $time = strtotime_match_format($page[0][0], $dateFormat);

        $standing[0]['Tournaments'][$time] = [
            'Time' => $time,
            'Date' => date($dateFormat, $time),
        ];

        for ($i = 4; $i < count($page); $i++) {
            $playerName = $page[$i][3];
            $playerTitle = $page[$i][2];
            $playerDate = $page[$i][6];
            $playerScore = $page[$i][10];
            $playerHash = md5($playerName.'|'.$playerDate);

            $standing[0]['Rows'][$playerHash]['Name'] = $playerName;
            $standing[0]['Rows'][$playerHash]['Title'] = $playerTitle;
            $standing[0]['Rows'][$playerHash][$time] =  $playerScore;
            $standing[0]['Rows'][$playerHash]['Count'] = $playerScore;
            $standing[0]['Rows'][$playerHash]['Score'] = $playerScore;
            $standing[0]['Rows'][$playerHash]['Bonus'] = $playerScore;
            $standing[0]['Rows'][$playerHash]['Total'] = $playerScore;

            $standing[$time]['Rows'][$playerHash]['Count'] = $playerScore;

            if (empty($standing[0]['Rows'][$playerHash]['Bonus'])) {
                $standing[0]['Rows'][$playerHash]['Bonus'] = 1;
            } else {
                $standing[0]['Rows'][$playerHash]['Bonus']++;
            }

            $standing[0]['Rows'][$playerHash]['Score'] = $playerScore;
            $standing[$time]['Rows'][$playerHash]['Name'] = $playerName;
            $standing[$time]['Rows'][$playerHash]['Title'] = $playerTitle;
            $standing[$time]['Rows'][$playerHash]['Score'] =  $playerScore;
            $standing[$time]['Rows'][$playerHash]['Total'] = $playerScore;
        }
    }
}

// Update ranks
$rank = 1;
foreach ($standing[0]['Rows'] as &$row) {
    $row['Rank'] = $rank;
    $rank++;
}

ksort($standing[0]['Tournaments']);

// Save file
file_put_contents($jsonPath.'/Standing.json', json_encode($standing, JSON_PRETTY_PRINT));
