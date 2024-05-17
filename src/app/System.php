<?php

namespace App;

class System
{
    public static function setLocale()
    {
        setlocale(LC_ALL, 'it_IT.UTF-8');
        setlocale(LC_TIME, 'it_IT.UTF-8');
        date_default_timezone_set('Europe/Rome');
    }
}
