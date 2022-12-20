<?php

$twig = Services::get('twig');

$html = $twig->render('index.html', ['the' => 'variables', 'go' => 'here']);

return $html;
