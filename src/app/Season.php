<?php

namespace App;

use Webmozart\Glob\Glob;

class Season
{
    public static function getSeason($year)
    {
        $csvDirectory  = 'src/seasons/'.$year.'/csv';
        $jsonDirectory = 'src/seasons/'.$year.'/json';

        return [
            'csv_dir' => $csvDirectory,
            'json_dir' => $jsonDirectory
        ];
    }

    /**
     * Create Rank for a season based on multiple standings.
     *
     * @param $year
     * @param $standings
     *
     * @return array
     */
    public static function createRank($year, $standings)
    {
        $trends = [];
        $championship = [
            'year' => $year
        ];

        foreach ($standings as $time => $standing) {
            Standing::applyStandingToRank($championship, $standing, $time);
        }

        Standing::applyRank($trends);
        Standing::applyRank($championship['general']['rows']);

        foreach ($championship['general']['rows'] as $key => &$row) {
            $row['trend'] = isset($trends[$key]) ? $trends[$key]['rank'] - $row['rank'] : 0;
        }

        foreach ($championship['stages'] as &$stage) {
            Standing::applyRank($stage['rows']);
        }

        ksort($championship['stages']);

        $championship['general']['rows'] = array_values($championship['general']['rows']);

        return $championship;
    }

    public static function loadRank($year)
    {
        $rankFile = __DIR__ . '/../seasons/' .$year.'/json/rank.json';

        return json_decode(file_get_contents($rankFile), true);
    }
}
