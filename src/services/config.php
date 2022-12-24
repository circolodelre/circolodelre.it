<?php

$settings = json_decode(file_get_contents('src/config.json'), true);

$settings['date_format'] = $settings['date_format'] ?? 'd/m/Y';
$settings['current_year'] = $settings['current_year'] ?? date('Y');

return $settings;