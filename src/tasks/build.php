<?php

require_once __DIR__.'/../../vendor/autoload.php';

use Webmozart\Glob\Glob;

echo "Build... \n";

$pagesDir = realpath(__DIR__.'/../pages');
$globPages = $pagesDir.'/**/*.php';

foreach (Glob::glob($globPages) as $file) {
    $relativeFile = substr($file, strlen($pagesDir) + 1);
    $relativeName = basename($relativeFile, '.php');
    $relativePath = dirname($relativeFile);

    $html = require_once $file;
    $page = __DIR__.'/../../docs/'.$relativePath.'/'.$relativeName.'.html';

    is_dir(dirname($page)) or mkdir(dirname($page), 0777, true);
    file_put_contents($page, $html);
}
