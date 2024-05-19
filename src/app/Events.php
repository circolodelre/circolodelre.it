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

    public static function loadEventByUrl($eventUrl)
    {
        $allEvents = self::loadEvents();

        foreach ($allEvents as $seasonEvents) {
            foreach ($seasonEvents as $event) {
                if ($event['url'] == $eventUrl) {
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

    public static function getType($type)
    {
        $type = strtolower($type);

        if (empty($type) || in_array($type, ['torneo', 'gara'])) {
            return 'tournament';
        } elseif (in_array($type, ['lezione', 'scuola'])) {
            return 'lesson';
        }

        return $type;
    }

    public static function parseCsvEvents($csv)
    {
        $config = Services::get('config');
        $now = time();

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
            $time = strtotime($date);
            $title =  $csv[$row][3];
            $link = trim($csv[$row][4] ?? '');

            if (str_starts_with($link, 'https://docs.google.com/document/d/')) {
                $link = preg_replace('/.*\/d\/([^\/]+)\/.*/', 'https://docs.google.com/document/d/$1/export?format=pdf', $link);
            }

            if (empty($link)) {
                continue;
            }

            $season = self::getSeason($date);
            $eventUniqueName = $title.' - Stagione '.$season;
            $eventSlug = $config['event_slug'].'/'.Functions::getSlug($season).'/'.Functions::getSlug($title);
            $eventUrl = '/'.$eventSlug.'.html';
            $flyerUrl = '/'.$eventSlug.'.pdf';
            $subscribeUrl = str_replace('{event}', urlencode($eventUniqueName), $config['subscribe']);
            $opening = $time - (40 * 24 * 60 * 60);
            $event = [
                'slug' => $eventSlug,
                'url' => $eventUrl,
                'flyerUrl' => $flyerUrl,
                'joinersUrl' => $config['joiners'],
                'subscribeUrl' => $subscribeUrl,
                'type' => self::getType($csv[$row][2]),
                'season' => $season,
                'title' => $title,
                'link' => $link,
                'uniqueName' => $eventUniqueName,
                'date' => $date,
                'status' => $now >= $opening ? 'open' : 'close',
                'opening' => $opening,
                'deadline' => $time - (24 * 60 * 60),
                'time' => $time,
            ];

            $events[] = $event;
        }

        return $events;
    }

    public static function loadNextTournament()
    {
        $events = self::loadEvents();

        foreach ($events as $season => $seasonEvents) {
            if (count($seasonEvents) > 0) {
                foreach ($seasonEvents as $event) {
                    if ($event['type'] == 'tournament') {
                        return $event;
                    }
                }
            }
        }
    }
}
