<?php

use App\Events;
use App\Services;

require_once __DIR__.'/../../vendor/autoload.php';

$config = Services::get('config');

echo "Download Events\n";
for ($key = 0; $key < 9; $key++) {
    if (file_exists(__DIR__.'/../events/config-'.$key.'.csv')) {
        unlink(__DIR__.'/../events/config-'.$key.'.csv');
    }
}
foreach ($config['events'] as $key => $file) {
    echo 'Download '.$file."\n";
    $csv = file_get_contents($file);
    $file = __DIR__.'/../events/config-'.$key.'.csv';
    file_put_contents($file, $csv);
    chmod($file, 0777);
}

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
