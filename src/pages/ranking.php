<?php

use App\Ranking;
use App\Services;
use App\System;

$twig = Services::get('twig');

System::setLocale();

$allYears = Ranking::loadYears();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
preg_match('#^/ranking/(\d{4})/?$#', $uri, $m);
$year = isset($m[1]) && in_array($m[1], $allYears) ? $m[1] : (end($allYears) ?: null);

$data = $year ? Ranking::loadYear($year) : ['rounds' => [], 'standings' => []];

return $twig->render('ranking.html', [
    'year'      => $year,
    'all_years' => $allYears,
    'rounds'    => $data['rounds'],
    'standings' => $data['standings'],
]);