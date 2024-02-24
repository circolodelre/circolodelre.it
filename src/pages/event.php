<?php

use App\Events;

$twig = services::get('twig');
$config = services::get('config');
$eventSlug = basename($_SERVER['REQUEST_URI'], '.html');
$event = Events::loadEventBySlug($eventSlug);

return $twig->render('pages/tournament-registration.html', [
    'year' => '2024',
    'today' => date($config['date_format']),
    'tournament' => 'campionato-provinciale-2023',
    'event' => $event
]);
