<?php

use App\GrandPrix;
use App\Services;
use App\System;

$twig = Services::get('twig');

System::setLocale();

$allSeasons = GrandPrix::loadSeasons();
$allYears   = array_keys($allSeasons);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
preg_match('#^/grandprix/(\d{4})/?$#', $uri, $m);
$year = isset($m[1]) && isset($allSeasons[$m[1]]) ? $m[1] : (end($allYears) ?: null);

$season = $year ? $allSeasons[$year] : ['tournaments' => []];

return $twig->render('grandprix.html', [
    'season'    => $season,
    'year'      => $year,
    'all_years' => $allYears,
]);
