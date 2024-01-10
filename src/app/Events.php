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

        foreach (scandir(__DIR__.'/../events') as $file) {
            if (substr($file, -4) == '.csv') {
                $csv = array_map('str_getcsv', file(__DIR__.'/../events/'.$file));
                $events = array_merge($events, self::parseCsvEvents($csv));
            }
        }

        return $events;
    }

    public static function loadEventBySlug($eventSlug)
    {
        foreach (self::loadEvents() as $event) {
            if ($event['slug'] == $eventSlug) {
                return $event;
            }
        }
    }

    public static function parseSeason($season)
    {
        if (preg_match('/[0-9]{4}\/[0-9]{2}/', $season, $matches)) {
            $season = $matches[0];
        }

        $season = explode('/', $season);

        if (strlen($season[1]) == 2) {
            $season[1] = '20'.$season[1];
        }

        return $season;
    }

    public static function convertDate($date, $season)
    {
        $date = strtolower($date);

        $date = str_replace(['gen'], 'january', $date);
        $date = str_replace(['feb'], 'february', $date);
        $date = str_replace(['mar'], 'march', $date);
        $date = str_replace(['apr'], 'april', $date);
        $date = str_replace(['mag'], 'may', $date);
        $date = str_replace(['giu'], 'june', $date);
        $date = str_replace(['lug'], 'july', $date);
        $date = str_replace(['ago'], 'august', $date);
        $date = str_replace(['set'], 'september', $date);
        $date = str_replace(['ott'], 'october', $date);
        $date = str_replace(['nov'], 'november', $date);
        $date = str_replace(['dic'], 'december', $date);

        $time = strtotime($date);
        $month = date('m', $time);
        $day = date('d', $time);

        if (date('m', $time) > 8) {
            return $season[0].'-'.$month.'-'.$day;
        }

        return $season[1].'-'.$month.'-'.$day;
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

            $title =  $csv[$row][3];
            $date = $csv[$row][0];
            $event = [
                'slug' => self::eventSlug($season[0].'-'.$season[1].'-'.$title),
                'type' => 'tournament',
                'season' => $season,
                'title' => $title,
                'date' => $date,
                'time' => strtotime($date),
            ];
        }

        return $events;
    }

    public static function eventSlug($text)
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
