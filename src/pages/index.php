<?php

use App\Season;

$twig = services::get('twig');
$config = services::get('config');
$year = 2018;

return $twig->render('index.html', [
    'year' => $year,
    'today' => date($config['date_format']),
    'rank' => Season::loadRank($year)
]);
