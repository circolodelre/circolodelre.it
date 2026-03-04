<?php

use App\Events;
use App\GrandPrix;
use App\Services;
use App\System;

$twig = services::get('twig');
$config = services::get('config');
$year = 2018;

System::setLocale();

$seasons = GrandPrix::loadSeasons();
$gpYear = (string) max(array_keys($seasons) ?: [date('Y')]);
$gpStandings = GrandPrix::computeStandings($seasons[$gpYear]['tournaments'] ?? []);

return $twig->render('index.html', [
    'year'          => $year,
    'today'         => date($config['date_format']),
    'events'        => Events::loadEvents(),
    'nextTournament' => Events::loadNextTournament(),
    'config'        => $config,
    'gpYear'        => $gpYear,
    'gpStandings'   => $gpStandings,
]);
