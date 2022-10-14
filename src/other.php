<?php



if ($_SERVER['SERVER_NAME'] != 'localhost'
    && (!(isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)
        || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'))) {
    $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $redirect);
    exit();
}



session_start();

$settings = circolodelre_load_settings();
circolodelre_load_language($settings['country']);


$container = $app->getContainer();
$container['view'] = new \Slim\Views\PhpRenderer('./templates');
$container['logger'] = function () {
    $logger = new \Monolog\Logger('debug');
    $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
    $logger->pushHandler(new \Monolog\Handler\StreamHandler(__DIR__.'/storage/log', \Monolog\Logger::ERROR));
    return $logger;
};

$app->get('/', function ($request, $response, $args) use ($settings) {
    $file = __DIR__ . '/storage/json/' .$settings['year'].'/Championship.json';

    return $this->view->render($response, 'index.phtml', [
        'settings' => $settings,
        'championship' => json_decode(file_get_contents($file), true),
    ]);
});

$app->get('/'._('standing'), function ($request, $response, $args) use ($settings) {
    $file = __DIR__ . '/storage/json/' .$settings['year'].'/Championship.json';

    return $this->view->render($response, 'index.phtml', [
        'settings' => $settings,
        'championship' => json_decode(file_get_contents($file), true),
    ]);
});

function _($a) {

}

$app->run();
