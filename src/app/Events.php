<?php

namespace App;

use Webmozart\Glob\Glob;

class Events
{
/**
     * Parse events from CSV.
     *
     * @param $csv
     *
     * @return array
     */
    public static function loadEvents()
    {
        $events = [];
        $eventsBySeason = [];

        foreach (scandir(__DIR__.'/../events') as $file) {
            if (substr($file, -4) == '.csv') {
                $csv = array_map('str_getcsv', file(__DIR__.'/../events/'.$file));
                $events = array_merge($events, self::parseCsvEvents($csv));
            }
        }

        $today = time();
        foreach ($events as $event) {
            if ($event['time'] < $today) {
                continue;
            }

            $season = $event['season'];
            if (empty($eventsBySeason[$season])) {
                $eventsBySeason[$season] = [];
            }

            $eventsBySeason[$season][] = $event;
        }

        return $eventsBySeason;
    }

    public static function loadEventBySlug($eventSlug)
    {
        foreach (self::loadEvents() as $seasonEvents) {
            foreach ($seasonEvents as $event) {
                if ($event['slug'] == $eventSlug) {
                    return $event;
                }
            }
        }
    }

    public static function getSeason($date)
    {
        $time = strtotime($date);
        $year = date('Y', $time);
        $month = date('m', $time);

        if ($month > 8) {
            return $year.'/'.($year + 1);
        }

        return ($year - 1).'/'.$year;
    }

    public static function parseCsvEvents($csv)
    {
/*
        echo "<pre>";
        var_dump($csv);
        echo "</pre>";
        die();
*/

        $events = [];
        //$season = self::parseSeason($csv[2][0]);

        for ($row = 1; $row < count($csv); $row++) {
            if (empty($csv[$row][0]) || empty($csv[$row][3]) ||
                !preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $csv[$row][0])) {
                continue;
            }

            $date = $csv[$row][0];
            $title =  $csv[$row][3];
            $season = self::getSeason($date);
            $event = [
                'slug' => self::getEventSlug($season.'-'.$title),
                'type' => 'tournament',
                'season' => $season,
                'title' => $title,
                'date' => $date,
                'time' => strtotime($date),
            ];

            $events[] = $event;
        }

        return $events;
    }

    public static function getEventSlug($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);
        // trim
        $text = trim($text, '-');
        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);
        // lowercase
        $text = strtolower($text);
        if (empty($text)) {
            return 'n-a';
        }
        return $text;
    }

}
