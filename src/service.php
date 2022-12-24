<?php

class service
{
    protected static $services = [];

    public static function get($service)
    {
        if (empty(self::$services[$service])) {
            self::$services[$service] = require_once __DIR__.'/services/'.$service.'.php';
        }

        return self::$services[$service];
    }
}
