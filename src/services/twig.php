<?php

use App\Services;

$config = Services::get('config');

$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/../views');
$twig = new \Twig\Environment($loader, ['cache' => false]);

$twig->addFunction(new \Twig\TwigFunction('_', function ($message) {
    return _($message);
}));

$twig->addFilter(new \Twig\TwigFilter('strftime', function ($date) use ($config) {
    return @strftime($config['date_format'], $date);
}));

return $twig;
