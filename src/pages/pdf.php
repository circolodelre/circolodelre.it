<?php

use App\Events;

$twig = services::get('twig');
$config = services::get('config');
$year = 2018;

return $twig->render('pdf.html', [
    'year' => $year,
    'today' => date($config['date_format']),
    'events' => Events::loadEvents()
]);
