<?php

use App\Events;
use App\Services;

$twig = services::get('twig');
$config = services::get('config');
$eventUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$event = Events::loadEventByUrl($eventUrl);

return $twig->render('event.html', [
    'year' => '2024',
    'today' => date($config['date_format']),
    'tournament' => 'campionato-provinciale-2023',
    'event' => $event
]);
