<?php

$twig = services::get('twig');
$config = services::get('config');
$year = $_SERVER['REQUEST_URI'];

var_dump($year);
die();

return $twig->render('season.html', [
    'year' => $year,
    'today' => date($config['date_format']),
    'rank' => \App\Season::loadRank($year)
]);
