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
    public static function createRank($year, $standings): array
    {
        $rank = [
            'year' => $year,
            'season' => $year,
            'trends' => []
        ];

        foreach ($standings as $time => $standing) {
            Standing::applyStandingToRank($rank, $standing, $time);
        }

        Standing::applyRank($rank['trends']);
        Standing::applyRank($rank['general']['rows']);

        foreach ($rank['general']['rows'] as $key => &$row) {
            $row['trend'] = isset($rank['trends'][$key]);
            $row['trend-var'] = isset($rank['trends'][$key]) ? $rank['trends'][$key]['rank'] - $row['rank'] : 0;
        }

        foreach ($rank['stages'] as &$stage) {
            Standing::applyRank($stage['rows']);
        }

        ksort($rank['stages']);

        $rank['general']['rows'] = array_values($rank['general']['rows']);

        return $rank;
    }

    public static function loadRank($year)
    {
        $rankFile = __DIR__ . '/../seasons/' .$year.'/json/rank.json';

        return json_decode(file_get_contents($rankFile), true);
    }
}
