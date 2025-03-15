<?php

use App\Events;
use App\Services;

require_once __DIR__.'/../../vendor/autoload.php';

$config = Services::get('config');

echo "Download Flyers\n";
$allEvents = Events::loadEvents();
if (empty($allEvents)) {
    echo "No flyers found\n";
    exit;
}
foreach ($allEvents as $seasonEvents) {
    foreach ($seasonEvents as $event) {
        echo 'Download '.$event['link']."\n";
        $pdf = file_get_contents($event['link']);
        $dir = __DIR__.'/../../docs'.dirname($event['flyerUrl']);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        file_put_contents(__DIR__.'/../../docs'.$event['flyerUrl'], $pdf);
    }
}
