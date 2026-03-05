<?php

namespace App;

class Services
{
    protected static $services = [];

    public static function get(string $service): mixed
    {
        if (empty(self::$services[$service])) {
            self::$services[$service] = require_once __DIR__ . '/../services/' . $service . '.php';
        }

        return self::$services[$service];
    }
}
