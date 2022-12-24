<?php

$settings = json_decode(file_get_contents('settings.json'), true);

$settings['date-format'] = $settings['date-format'] ?? 'd/m/Y';
$settings['current-year'] = $settings['current-year'] ?? date('Y');

return [
    "country" => "it",
    "date-format" => "d/m/Y",
    "tournaments-path" => "~/Dropbox/Tornei/{YEAR}"
];
