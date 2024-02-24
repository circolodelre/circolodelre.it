<?php

use App\Events;

require_once __DIR__.'/../../vendor/autoload.php';

$config = services::get('config');

echo "Download events\n";
foreach ($config['events'] as $key => $file) {
    #$csv = file_get_contents($file);
    #file_put_contents(__DIR__.'/../events/config-'.$key.'.csv', $csv);
}