<?php

$page = rtrim(__DIR__.'/pages'.parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

$file = preg_replace('/.php$/', '.html', $page);

var_dump($file);