<?php



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



