<?php

$twig = services::get('twig');
$config = services::get('config');
$year = intval(basename($_SERVER['REQUEST_URI'], '.html')) ?: $config['year'];

return $twig->render('season.html', [
    'year' => $year,
    'today' => date($config['date_format']),
    'rank' => \App\Season::loadRank($year)
]);
