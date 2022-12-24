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
        $csvFiles = scandir_csv($csvPath);
        $standings = [];

        if (empty($csvFiles)) {
            return $standings;
        }

        foreach ($csvFiles as $file) {
            if (!preg_match('/^(.*)-Standing\\.csv$/', $file, $name)) {
                continue;
            }

            $players = vegachess_get_players_csv($name[1].'-Players.csv');
            $standing = vegachess_get_standing_csv($file);
            foreach ($standing['rows'] as &$row) {
                $row['gender'] = $players['rows'][$row['id']]['gender'];
            }
            $time = strtotime_match_format($standing['name'], $dateFormat);
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
}
