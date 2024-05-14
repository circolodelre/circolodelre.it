<?php


class Vega
{
    /**
     * @param $file
     * @return array
     * @throws \App\Exception
     */
    public static function getStandingFromCsv($file)
    {
        if (!file_exists($file)) {
            throw new \App\Exception("file not found.\n");
        }

        $page = [];
        $read = fopen($file, 'r');

        while (($line = fgetcsv($read, 0, ';')) !== false) {
            $page[] = $line;
        }

        fclose($read);

        $standing = [
            'name' => trim($page[0][0]),
            'rows' => [],
        ];

        foreach ($page[3] as &$field) {
            $field = str_replace(' ', '-', strtolower(trim($field)));
        }

        for ($i = 4; $i < count($page); $i++) {
            $row = [];

            foreach ($page[3] as $c => $field) {
                $row[$field] = trim($page[$i][$c]);
            }

            $row['key'] = md5($row['name'] . '|' . $row['birth']);
            $standing['rows'][$row['key']] = $row;
        }

        return $standing;
    }

    /**
     * @param $file
     * @return array
     * @throws \App\Exception
     */
    public static function getPlayersFromCsv($file)
    {
        if (!file_exists($file)) {
            throw new \App\Exception("file not found.\n");
        }

        $page = [];
        $read = fopen($file, 'r');

        while (($line = fgetcsv($read, 0, ';')) !== false) {
            $page[] = $line;
        }

        fclose($read);

        $players = [
            'name' => trim($page[0][0]),
            'rows' => [],
        ];

        foreach ($page[2] as &$field) {
            $field = str_replace(' ', '-', strtolower(trim($field)));
        }

        for ($i = 3; $i < count($page); $i++) {
            $row = [];

            foreach ($page[2] as $c => $field) {
                $row[$field] = trim($page[$i][$c]);
            }

            $players['rows'][$row['n']] = $row;
        }

        return $players;
    }
}
