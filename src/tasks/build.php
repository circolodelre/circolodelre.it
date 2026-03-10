<?php

use App\Events;
use App\GrandPrix;
use App\Ranking;
use App\Services;
use App\System;

require_once __DIR__.'/../../vendor/autoload.php';

$config = Services::get('config');

System::setLocale();

echo "Build... \n";

foreach ($config['pages'] as $page => $file) {
    $_SERVER['REQUEST_URI'] = $page;

    $html = require $file;
    $path = __DIR__.'/../../docs/'.$page;

    is_dir(dirname($path)) or mkdir(dirname($path), 0777, true);
    file_put_contents($path, $html);
}

$seasons = GrandPrix::loadSeasons();
foreach (array_keys($seasons) as $year) {
    $file = __DIR__.'/../pages/grandprix.php';
    $page = '/grandprix/'.$year.'/';
    $_SERVER['REQUEST_URI'] = $page;

    $html = require $file;
    $path = __DIR__.'/../../docs/grandprix/'.$year.'/index.html';

    is_dir(dirname($path)) or mkdir(dirname($path), 0777, true);
    file_put_contents($path, $html);
    echo "  grandprix/$year/\n";
}

foreach (Ranking::loadYears() as $year) {
    $file = __DIR__.'/../pages/ranking.php';
    $page = '/ranking/'.$year.'/';
    $_SERVER['REQUEST_URI'] = $page;

    $html = require $file;
    $path = __DIR__.'/../../docs/ranking/'.$year.'/index.html';

    is_dir(dirname($path)) or mkdir(dirname($path), 0777, true);
    file_put_contents($path, $html);
    echo "  ranking/$year/\n";
}

$events = Events::loadEvents();
foreach ($events as $season => $seasonEvents) {
    foreach ($seasonEvents as $event) {
        $file = './src/pages/events.php';
        $page = '/'.$event['slug'].'.html';
        $_SERVER['REQUEST_URI'] = $page;

        $html = require $file;
        $path = __DIR__.'/../../docs/'.$page;

        is_dir(dirname($path)) or mkdir(dirname($path), 0777, true);
        file_put_contents($path, $html);
    }
}
