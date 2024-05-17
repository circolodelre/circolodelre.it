<?php

$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/../views');
$twig = new \Twig\Environment($loader, ['cache' => false]);

$twig->addFunction(new \Twig\TwigFunction('_', function ($message) {
    return _($message);
}));

$twig->addFilter(new \Twig\TwigFilter('strftime', function ($date, $format) {
    return @strftime($format, $date);
}));

return $twig;
