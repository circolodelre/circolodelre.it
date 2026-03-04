<?php

use App\GrandPrix;
use App\Services;
use App\System;

$twig = Services::get('twig');

System::setLocale();

$seasons = GrandPrix::loadSeasons();

return $twig->render('grandprix.html', ['seasons' => $seasons]);