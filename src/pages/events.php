<?php

use App\Events;
use App\Services;

$twig   = Services::get('twig');
$config = Services::get('config');
$eventUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$event = Events::loadEventByUrl($eventUrl);

return $twig->render('event.html', [
    'today' => date($config['date_format']),
    'event' => $event,
]);
