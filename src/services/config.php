<?php

use App\Functions;
use Webmozart\Glob\Glob;

$config = json_decode(file_get_contents('src/config.json'), true);

$config['date_format'] = $config['date_format'] ?? 'd/m/Y';
$config['current_year'] = $config['current_year'] ?? date('Y');
$config['event_slug'] = Functions::getSlug($config['event_slug'] ?? 'event');
$config['collections'] = $config['collections'] ?? [];

if (empty($config['pages'])) {
    $config['pages'] = [];
}

// PHP-based pages
$pagesDir = realpath(__DIR__.'/../pages');
$globPages = $pagesDir.'/**/*.php';
foreach (Glob::glob($globPages) as $file) {
    $path = '/'.substr($file, strlen($pagesDir) + 1);
    $name = basename($path, '.php');
    if (in_array($name, $config['collections'])) {
        continue;
    }
    $page = rtrim(dirname($path), '/').'/'.$name.'.html';
    $config['pages'][$page] = $file;
}

return $config;
