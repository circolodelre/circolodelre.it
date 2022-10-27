<?php

if (PHP_SAPI == 'cli-server') {
    $file = __DIR__ . '/..' . $_SERVER['REQUEST_URI'];
    if (is_file($file)) {
        $contentType = preg_match('/\.css$/i', $file) ? 'text/css' : mime_content_type($file);
        header('Content-Type: '.$contentType);
        readfile($file);
        return true;
    }
}

require_once __DIR__ . '/index.php';
