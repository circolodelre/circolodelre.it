<?php

require_once __DIR__.'/../../vendor/autoload.php';

$config = services::get('config');

echo "Build... \n";

foreach ($config['pages'] as $page => $file) {
    $_SERVER['REQUEST_URI'] = $page;

    $html = require_once $file;
    $path = __DIR__.'/../../docs/'.$page;

    is_dir(dirname($path)) or mkdir(dirname($path), 0777, true);
    file_put_contents($path, $html);
}
