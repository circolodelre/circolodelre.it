<?php

$twig = services::get('twig');
$config = services::get('config');
$year = 2018;


if (is_dir($path)) {

} else {

}
var_dump();

var_dump($_SERVER);

return $twig->render('index.html', [
    'year' => $year,
    'today' => date($config['date_format']),
    'rank' => \App\Season::loadRank($year)
]);
