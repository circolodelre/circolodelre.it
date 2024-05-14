<?php

use App\Services;

$twig = services::get('twig');
$config = services::get('config');
$year = 2018;

return $twig->render('category.html', [
    'year' => $year,
    'today' => date($config['date_format'])
]);
