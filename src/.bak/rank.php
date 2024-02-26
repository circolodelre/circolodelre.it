<?php

require_once __DIR__.'/../../vendor/autoload.php';

use App\Season;
use App\Services;
use bak\Standing;

$config = services::get('config');

$year = 2018;
$season = Season::getSeason($year);

echo "Season: {$year}\n";

$standings = Standing::loadSeason($season['csv_dir'], $config['date_format']);

if (empty($standings)) {
    die("File error: Standing files not found.\n");
}

$rank = Season::createRank($year, $standings);

is_dir($season['json_dir']) or mkdir($season['json_dir'], 0777, true);

$size = file_put_contents(
    $season['json_dir'].'/rank.json',
    json_encode($rank, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
);

echo "Championship file updated.\n";
