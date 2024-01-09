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

        /*
        $rankFile = __DIR__ . '/../seasons/' .$year.'/json/rank.json';

        return json_decode(file_get_contents($rankFile), true);
        */

        return $events;
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
        $season = self::parseSeason($csv[2][0]);

        for ($row = 4; $row < count($csv); $row++) {
            for ($col = 1; $col < count($csv[$row]); $col = $col + 3) {
                if (empty($csv[$row][$col]) || empty($csv[$row][$col + 1])) {
                    continue;
                }

                $date = self::convertDate($csv[$row][$col], $season);
                $event = [
                    'type' => 'tournament',
                    'season' => $season,
                    'title' => $csv[$row][$col + 1],
                    'date' => $date,
                    'time' => strtotime($date),
                ];

                $events[] = $event;
            }
        }

        return $events;
    }
}
