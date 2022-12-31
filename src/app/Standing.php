<?php

namespace App;

use Webmozart\Glob\Glob;

class Standing
{
    /**
     * @param $csvPath
     * @param $dateFormat
     *
     * @return array
     */
    public static function loadSeason($csvPath, $dateFormat): array
    {
        $last = 0;
        $csvFiles = Functions::scanCsv($csvPath);
        $standings = [];

        if (empty($csvFiles)) {
            return $standings;
        }

        foreach ($csvFiles as $file) {
            if (!preg_match('/^(.*)-Standing\\.csv$/', $file, $name)) {
                continue;
            }

            $players = Vega::getPlayersFromCsv($name[1].'-Players.csv');
            $standing = Vega::getStandingFromCsv($file);
            foreach ($standing['rows'] as &$row) {
                $row['gender'] = $players['rows'][$row['id']]['gender'];
            }
            $time = Functions::timeByFormat($standing['name'], $dateFormat);
            preg_match('/#([0-9]+)/', $standing['name'], $number);
            $standing['last'] = false;
            $standing['date'] = date($dateFormat, $time);
            $standing['number'] = isset($number[1]) ? $number[0] : $standing['date'];
            $standings[$time] = $standing;
            $last = $time > $last ? $time : $last;
        }

        $standings[$last]['last'] = true;

        ksort($standings);

        return $standings;
    }

    /**
     * @param $row0
     * @param $row1
     * @return int
     */
    public static function sortRank($row0, $row1)
    {
        return $row0['total'] > $row1['total'] ? -1 : 1;
    }

    /**
     * @param $standing
     */
    public static function applyRank(&$standing)
    {
        uasort($standing, '\\App\\Standing::sortRank');

        $rank = 1;
        foreach ($standing as &$row) {
            $row['rank'] = $rank;
            $rank++;
        }
    }

    /**
     *
     */
    public static function applyStandingToRank(&$rank, $standing, $time)
    {
        // general stages championship info
        $rank['stages'][$time] = [
            'time'   => $time,
            'date'   => $standing['date'],
            'number' => $standing['number'],
            'rows'   => [],
        ];

        // loop through standing rows
        foreach ($standing['rows'] as $row0) {
            $row = &$rank['general']['rows'][$row0['key']];
            $row['count']  = isset($row['count']) ? $row['count'] + 1 : 1;
            $row['player'] = $row0['name'];
            $row['gender'] = $row0['gender'];
            $row['title']  = $row0['title'];
            $row[$time]    = $row0['score'];

            $score = isset($row['score']) ? $row['score'] + $row0['score'] : $row0['score'];

            $row['score'] = number_format($score, 1);
            $row['bonus']  = number_format($row['count'] > 3 ? $row['count'] + 3 : $row['count'], 1);
            $row['total']  = number_format($row['score'] + $row['bonus'], 1);

            $kappa = 38;
            $turns = 5;
            $rating = round(1440 + ($row['score'] - ($turns * $row['count'] / 2)) * $kappa);
            $row['rating-var'] = sprintf('%+d', isset($row['rating']) ? $rating - $row['rating'] : $rating - 1440);
            $row['rating'] = $rating;

            $row = &$rank['stages'][$time]['rows'][$row0['key']];
            $row['player'] = $row0['name'];
            $row['gender']  = $row0['gender'];
            $row['title']  = $row0['title'];
            $row['total']  = $row0['score'];
            $row['buc1']   = $row0['buc1'];
            $row['buct']   = $row0['buct'];

            if (!$standing['last']) {
                $row = &$rank['trends'][$row0['key']];
                $row['count']  = isset($row['count']) ? $row['count'] + 1 : 1;
                $row['score']  = number_format(isset($row['score']) ? $row['score'] + $row0['score'] : $row0['score'], 1);
                $row['bonus']  = number_format($row['count'] > 3 ? $row['count'] + 3 : $row['count'], 1);
                $row['total']  = number_format($row['score'] + $row['bonus'], 1);
            }
        }
    }
}
