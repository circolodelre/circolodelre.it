<?php

$twig = services::get('twig');
$config = services::get('config');
$year = 2018;

return $twig->render('season.html', [
    'year' => $year,
    'today' => date($config['date_format']),
    'rank' => \App\Season::loadRank($year)
]);
