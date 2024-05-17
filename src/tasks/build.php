<?php

use App\Events;
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
