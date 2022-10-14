<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Europe/Rome');

require __DIR__ . '/../vendor/autoload.php';

$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true,
    ],
]);


$app->get('/', function ($request, $response, $args) use ($settings) {
    $file = __DIR__ . '/storage/json/' .$settings['year'].'/Championship.json';

    return $this->view->render($response, 'index.phtml', [
        'settings' => $settings,
        'championship' => json_decode(file_get_contents($file), true),
    ]);
});



