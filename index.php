<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Europe/Rome');

if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $file = __DIR__ . $_SERVER['REQUEST_URI'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/vendor/autoload.php';

session_start();

$settings = circolodelre_load_settings();

$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true,
    ],
]);

$container = $app->getContainer();
$container['view'] = new \Slim\Views\PhpRenderer('./templates');
$container['logger'] = function () {
    $logger = new \Monolog\Logger('debug');
    $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
    $logger->pushHandler(new \Monolog\Handler\StreamHandler(__DIR__.'/storage/log', \Monolog\Logger::ERROR));
    return $logger;
};

$app->get('/', function ($request, $response, $args) use ($settings) {
    $file = __DIR__.'/storage/json/'.$settings['year'].'/Championship.json';

    return $this->view->render($response, 'index.phtml', [
        'settings' => $settings,
        'championship' => json_decode(file_get_contents($file), true),
    ]);
});

$app->run();
