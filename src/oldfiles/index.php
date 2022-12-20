<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Europe/Rome');

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();
$twig = Twig::create('templates', ['cache' => false]);

$app->addRoutingMiddleware();
$app->add(TwigMiddleware::create($app, $twig));

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$app->get('/', function (Request $request, Response $response, $args) {
    $view = Twig::fromRequest($request);

    $settings = [
        'year' => 2022
    ];
    $file = __DIR__ . '/storage/json/' .$settings['year'].'/Championship.json';
#var_dump($_SERVER);

    return $view->render($response, 'index.html', [
        'settings' => $settings,
        #'championship' => json_decode(file_get_contents($file), true),
    ]);
});

$app->run();
