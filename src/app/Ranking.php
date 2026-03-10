<?php

namespace App;

class Ranking
{
    const K_FACTOR = 20;

    private static function defaultElo(): int
    {
        $config = Services::get('config');
        return (int) ($config['default_elo'] ?? 1500);
    }

    public static function loadYears(): array
    {
        $dir = __DIR__ . '/../ranking';
        $years = [];

        foreach (glob($dir . '/*', GLOB_ONLYDIR) as $yearDir) {
            $years[] = basename($yearDir);
        }

        sort($years);
        return $years;
    }

    public static function loadYear(string $year): array
    {
        $dir = __DIR__ . '/../ranking/' . $year;

        $eloFile = $dir . '/' . $year . '.elo';
        $initialRatings = self::loadEloFile($eloFile);

        $files = glob($dir . '/*.txt');
        sort($files);

        $rounds = [];
        foreach ($files as $file) {
            $round = self::parseRoundFile($file);
            if ($round) $rounds[] = $round;
        }

        $standings = self::computeStandings($rounds, $initialRatings);

        return [
            'rounds'   => $rounds,
            'standings' => $standings,
        ];
    }

    private static function loadEloFile(string $file): array
    {
        if (!is_file($file)) return [];

        $ratings = [];
        foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);
            if ($line === '') continue;
            $lastSpace = strrpos($line, ' ');
            if ($lastSpace === false) continue;
            $name   = trim(substr($line, 0, $lastSpace));
            $rating = (int) trim(substr($line, $lastSpace + 1));
            if ($name !== '') {
                $ratings[$name] = $rating;
            }
        }
        return $ratings;
    }

    public static function parseRoundFile(string $file): ?array
    {
        $lines = file($file, FILE_IGNORE_NEW_LINES);
        if (empty($lines)) return null;

        $name        = trim($lines[0]);
        $locationDate = trim($lines[1] ?? '');

        $location = $date = '';
        if (preg_match('/^(.+?)\s*-\s*(.+)$/', $locationDate, $m)) {
            $location = trim($m[1]);
            $date     = trim($m[2]);
        }

        $matches = [];
        foreach ($lines as $i => $line) {
            if ($i < 2) continue;
            $line = trim($line);
            if ($line === '') continue;

            if (preg_match('/^(.+?)\s+vs\s+(.+?)\s+([\d.½]+)\s*-\s*([\d.½]+)\s*$/', $line, $m)) {
                $matches[] = [
                    'white'       => trim($m[1]),
                    'black'       => trim($m[2]),
                    'score_white' => self::parseScore($m[3]),
                    'score_black' => self::parseScore($m[4]),
                ];
            }
        }

        return [
            'id'       => basename($file, '.txt'),
            'name'     => $name,
            'location' => $location,
            'date'     => $date,
            'matches'  => $matches,
        ];
    }

    private static function parseScore(string $s): float
    {
        $s = trim($s);
        if ($s === '½') return 0.5;
        return (float) $s;
    }

    public static function computeStandings(array $rounds, array $initialRatings): array
    {
        $ratings = $initialRatings;
        $players = [];

        foreach ($rounds as $round) {
            foreach ($round['matches'] as $match) {
                $white = $match['white'];
                $black = $match['black'];

                $ra = $ratings[$white] ?? self::defaultElo();
                $rb = $ratings[$black] ?? self::defaultElo();

                $ea = 1 / (1 + pow(10, ($rb - $ra) / 400));

                $deltaW = (int) round(self::K_FACTOR * ($match['score_white'] - $ea));
                $deltaB = (int) round(self::K_FACTOR * ($match['score_black'] - (1 - $ea)));

                if (!isset($players[$white])) {
                    $players[$white] = ['name' => $white, 'initial' => $ra, 'rounds' => []];
                }
                if (!isset($players[$black])) {
                    $players[$black] = ['name' => $black, 'initial' => $rb, 'rounds' => []];
                }

                $players[$white]['rounds'][] = [
                    'round_id'   => $round['id'],
                    'round_name' => $round['name'],
                    'round_date' => $round['date'],
                    'opponent'   => $black,
                    'color'      => 'white',
                    'score'      => $match['score_white'],
                    'rating_before' => $ra,
                    'delta'      => $deltaW,
                ];

                $players[$black]['rounds'][] = [
                    'round_id'   => $round['id'],
                    'round_name' => $round['name'],
                    'round_date' => $round['date'],
                    'opponent'   => $white,
                    'color'      => 'black',
                    'score'      => $match['score_black'],
                    'rating_before' => $rb,
                    'delta'      => $deltaB,
                ];

                $ratings[$white] = $ra + $deltaW;
                $ratings[$black] = $rb + $deltaB;
            }
        }

        // Include players with initial ratings but no matches
        foreach ($initialRatings as $name => $rating) {
            if (!isset($players[$name])) {
                $players[$name] = ['name' => $name, 'initial' => $rating, 'rounds' => []];
            }
        }

        $standings = [];
        foreach ($players as $name => $data) {
            $current = $ratings[$name] ?? $data['initial'];
            $standings[] = [
                'name'    => $name,
                'rating'  => $current,
                'initial' => $data['initial'],
                'delta'   => $current - $data['initial'],
                'rounds'  => $data['rounds'],
            ];
        }

        usort($standings, fn($a, $b) => $b['rating'] <=> $a['rating']);

        return array_values($standings);
    }
}