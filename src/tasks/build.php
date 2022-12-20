<?php

require_once __DIR__.'/../../vendor/autoload.php';

echo "Build... \n";

// index.html
$html = require_once __DIR__.'/../pages/index.php';
$file = __DIR__.'/../../docs/index.html';
file_put_contents($file, $html);
