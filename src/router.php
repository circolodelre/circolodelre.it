<?php

if (PHP_SAPI == 'cli-server') {
    $file = __DIR__ . '/..' . $_SERVER['REQUEST_URI'];
    if (is_file($file)) {
        header('Content-Type: '.mime_content_type($file));
        readfile($file);
        return false;
    }
}

require_once __DIR__ . '/index.php';
