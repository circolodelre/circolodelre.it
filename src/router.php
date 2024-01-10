<?php

error_reporting(E_ALL);
ini_set('display_errors', true);

if (PHP_SAPI == 'cli-server') {
    $file = __DIR__ . '/../docs' . $_SERVER['REQUEST_URI'];
    if (!preg_match('/.html$/i', $file) && is_file($file)) {
        $contentType = preg_match('/\.css$/i', $file) ? 'text/css' : mime_content_type($file);
        header('Content-Type: '.$contentType);
        readfile($file);
        return true;
    }
}

require_once __DIR__.'/../vendor/autoload.php';

$page = __DIR__.'/pages'.parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = preg_replace('/.html$/', '.php', rtrim($page, '/'));
$file = is_dir($path) ? $path.'/index.php' : (is_file($path) ? $path : dirname($path).'/index.php');

$html = require_once $file;

echo $html;
