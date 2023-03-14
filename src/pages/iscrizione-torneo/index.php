<?php

$twig = services::get('twig');
$config = services::get('config');
$year = intval(basename($_SERVER['REQUEST_URI'], '.html')) ?: $config['year'];

return $twig->render('pages/tournament-registration.html', [
    'year' => $year,
    'today' => date($config['date_format']),
    'tournament' => 'campionato-provinciale-2023'
]);
