<?php

namespace App;

class GrandPrix
{
    public static function loadSeasons(): array
    {
        $seasonsDir = __DIR__ . '/../seasons';
        $seasons = [];

        foreach (glob($seasonsDir . '/*', GLOB_ONLYDIR) as $yearDir) {
            $year = basename($yearDir);
            $tournaments = [];

            $files = glob($yearDir . '/*.txt');
            sort($files);

            foreach ($files as $file) {
                $t = self::parseTournamentFile($file);
                if ($t) $tournaments[] = $t;
            }

            $seasons[$year] = ['tournaments' => $tournaments];
        }

        ksort($seasons);
        return $seasons;
    }

    public static function computeStandings(array $tournaments): array
    {
        $standings = [];

        foreach ($tournaments as $t) {
            foreach ($t['players'] as $p) {
                if ($p['gp_points'] > 0) {
                    if (!isset($standings[$p['name']])) {
                        $standings[$p['name']] = ['name' => $p['name'], 'total' => 0, 'results' => []];
                    }
                    $standings[$p['name']]['total'] += $p['gp_points'];
                    $standings[$p['name']]['results'][] = [
                        'id'   => $t['id'],
                        'name' => $t['name'],
                        'pts'  => $p['gp_points'],
                    ];
                }
            }
        }

        usort($standings, fn($a, $b) => $b['total'] <=> $a['total']);
        return array_values($standings);
    }

    public static function parseTournamentFile(string $file): ?array
    {
        $lines = file($file, FILE_IGNORE_NEW_LINES);
        if (empty($lines)) return null;

        $tournamentName = trim($lines[0]);
        $locationDate = trim($lines[1] ?? '');

        $location = $date = '';
        if (preg_match('/^(.+?)\s*-\s*(.+)$/', $locationDate, $m)) {
            $location = trim($m[1]);
            $date = trim($m[2]);
        }

        $gpPoints = self::loadGpPoints(substr($file, 0, -4) . '.pts');

        $players = [];
        $numRounds = 0;
        $tiebreakNames = [];

        foreach ($lines as $line) {
            // Detect header line to extract tiebreak column names
            if (preg_match('/^Pos\s+NAME/i', $line)) {
                $parts = explode('|', $line);
                if (isset($parts[2])) {
                    $tiebreakNames = array_values(array_filter(preg_split('/\s+/', trim($parts[2]))));
                }
                continue;
            }

            if (!preg_match('/^\s{1,3}\d+\s/', $line)) continue;

            $parts = explode('|', $line);
            if (count($parts) < 3) continue;

            $info      = $parts[0];
            $roundsStr = $parts[1];
            $tbStr     = $parts[2];

            if (!preg_match('/^\s*(\d+)\s+(.+?)\s{2,}(\d+)\s+\S+\s+\S+\s+([\d.]+)\s*$/', $info, $m)) continue;

            $pos    = (int)$m[1];
            $name   = trim($m[2]);
            $rating = (int)$m[3];
            $pts    = (float)$m[4];

            $rounds = array_values(array_filter(preg_split('/\s+/', trim($roundsStr))));
            if (count($rounds) > $numRounds) $numRounds = count($rounds);

            $tiebreaks = array_values(array_filter(preg_split('/\s+/', trim($tbStr))));

            $players[] = [
                'pos'       => $pos,
                'name'      => $name,
                'gp_points' => $gpPoints[$name] ?? 0,
                'rating'    => $rating,
                'pts'       => $pts,
                'rounds'    => $rounds,
                'tiebreaks' => $tiebreaks,
            ];
        }

        return [
            'id'             => basename($file, '.txt'),
            'name'           => $tournamentName,
            'location'       => $location,
            'date'           => $date,
            'num_rounds'     => $numRounds,
            'tiebreak_names' => $tiebreakNames,
            'players'        => $players,
        ];
    }

    private static function loadGpPoints(string $ptsFile): array
    {
        if (!is_file($ptsFile)) return [];

        $points = [];
        foreach (file($ptsFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);
            if ($line === '') continue;
            $lastSpace = strrpos($line, ' ');
            if ($lastSpace === false) continue;
            $name = trim(substr($line, 0, $lastSpace));
            $pts  = (int) trim(substr($line, $lastSpace + 1));
            if ($name !== '' && $pts > 0) {
                $points[$name] = $pts;
            }
        }
        return $points;
    }
}