<?php

use App\Events;
use App\Services;


require_once __DIR__.'/../../vendor/autoload.php';

$config = Services::get('config');

echo "Download events\n";
foreach ($config['events'] as $key => $file) {
    #$csv = file_get_contents($file);
    #file_put_contents(__DIR__.'/../events/config-'.$key.'.csv', $csv);
}

echo "Download event flyers\n";
$allEvents = Events::loadEvents();
foreach ($allEvents as $seasonEvents) {
    foreach ($seasonEvents as $event) {
        if (empty($event['link'])) {
            continue;
        }

        $pdf = file_get_contents($event['link']);

        file_put_contents(__DIR__.'/../../docs'.$event['flyerUrl'], $pdf);
    }
}
