<?php

error_reporting(E_ALL);
ini_set('display_errors', true);

if (PHP_SAPI == 'cli-server') {
    $file = __DIR__ . '/../docs' . $_SERVER['REQUEST_URI'];
    if (is_file($file)) {
        $contentType = preg_match('/\.css$/i', $file) ? 'text/css' : mime_content_type($file);
        header('Content-Type: '.$contentType);
        readfile($file);
        return true;
    }
}

#var_dump($_SERVER['REQUEST_URI']);

$requestUri = $_SERVER['REQUEST_URI'];

require_once __DIR__.'/../vendor/autoload.php';

$html = require_once __DIR__.'/pages/index.php';

echo $html;

