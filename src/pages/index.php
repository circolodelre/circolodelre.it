<?php

use App\Events;
use App\Services;
use App\System;

$twig = services::get('twig');
$config = services::get('config');
$year = 2018;

System::setLocale();

return $twig->render('index.html', [
    'year' => $year,
    'today' => date($config['date_format']),
    'events' => Events::loadEvents(),
    'config' => $config,
]);
