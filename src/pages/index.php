<?php

use App\Events;
use App\GrandPrix;
use App\Ranking;
use App\Services;
use App\System;

$twig = services::get('twig');
$config = services::get('config');
$year = 2018;

System::setLocale();

$seasons = GrandPrix::loadSeasons();
$gpYear = (string) max(array_keys($seasons) ?: [date('Y')]);
$gpStandings = GrandPrix::computeStandings($seasons[$gpYear]['tournaments'] ?? []);

$gpAlboOro = [];
foreach ($seasons as $seasonYear => $data) {
    if ((string)$seasonYear === $gpYear) continue;
    $standings = GrandPrix::computeStandings($data['tournaments']);
    if (!empty($standings)) {
        $gpAlboOro[] = ['year' => (string)$seasonYear, 'name' => $standings[0]['name'], 'total' => $standings[0]['total']];
    }
}
usort($gpAlboOro, fn($a, $b) => $b['year'] <=> $a['year']);

$rankingYears = Ranking::loadYears();
$rankingYear  = end($rankingYears) ?: date('Y');
$rankingData  = $rankingYear ? Ranking::loadYear((string) $rankingYear) : ['standings' => []];
$rankingTop   = array_slice($rankingData['standings'], 0, 3);

return $twig->render('index.html', [
    'year'          => $year,
    'today'         => date($config['date_format']),
    'events'        => Events::loadEvents(),
    'nextTournament' => Events::loadNextTournament(),
    'config'        => $config,
    'gpYear'        => $gpYear,
    'gpStandings'   => $gpStandings,
    'gpAlboOro'     => $gpAlboOro,
    'rankingYear'   => $rankingYear,
    'rankingTop'    => $rankingTop,
]);
