<?php

$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/../templates');
$twig = new \Twig\Environment($loader, ['cache' => false]);

$environment = $twig->getEnvironment();

$environment->addFunction(new TwigFunction('__', function ($message) {
    return __($message);
}));

return $twig;
