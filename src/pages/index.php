<?php

$twig = services::get('twig');

$html = $twig->render('index.html', ['the' => 'variables', 'go' => 'here']);

return $html;
