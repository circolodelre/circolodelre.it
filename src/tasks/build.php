<?php

use App\Events;

require_once __DIR__.'/../../vendor/autoload.php';

$config = services::get('config');

echo "Download events\n";
foreach ($config['events'] as $key => $file) {
#    $csv = file_get_contents($file);
#    file_put_contents(__DIR__.'/../events/config-'.$key.'.csv', $csv);
}

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
        $file = './src/pages/iscrizione-torneo/index.php';
        $page = '/iscrizione-torneo/'.$event['slug'].'.html';
        $_SERVER['REQUEST_URI'] = $page;

        $html = require $file;
        $path = __DIR__.'/../../docs/'.$page;

        is_dir(dirname($path)) or mkdir(dirname($path), 0777, true);
        file_put_contents($path, $html);
    }
}
