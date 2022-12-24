<?php

$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/../templates');
$twig = new \Twig\Environment($loader, ['cache' => false]);

$twig->addFunction(new \Twig\TwigFunction('_', function ($message) {
    return _($message);
}));

return $twig;
