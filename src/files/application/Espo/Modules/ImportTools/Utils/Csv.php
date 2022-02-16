<?php
/************************************************************************
 * This file is part of Import Tools extension for EspoCRM.
 *
 * Import Tools extension for EspoCRM.
 * Copyright (C) 2014-2022 Yurii Kuznietsov, Taras Machyshyn, Oleksii Avramenko
 * Website: https://www.espocrm.com
 *
 * Import Tools extension is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Import Tools extension is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with EspoCRM. If not, see http://www.gnu.org/licenses/.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 ************************************************************************/

namespace Espo\Modules\ImportTools\Utils;

class Csv
{
    /**
     * Generate an associative array from the csv row data
     */
    public static function toAssocFromRow(array $row, array $headerList): array
    {
        return array_combine($headerList, $row);
    }

    /**
     * Generate row from an associative array
     */
    public static function toRowFromAssoc(array $assocData, array $headerList): array
    {
        $data = [];

        foreach ($headerList as $headerName) {
            $data[$headerName] = $assocData[$headerName] ?? null;
        }

        return array_values($data);
    }

    /**
     * Get header list from .csv file
     */
    public static function getHeaderList(string $src, string $delimiter): array
    {
        if (!file_exists($src)) {
            return [];
        }

        $fp = fopen($src, "r");

        $headerList = [];

        while (($row = fgetcsv($fp, 0, $delimiter)) !== FALSE) {
            $headerList = $row;
            break;
        }

        fclose($fp);

        return $headerList;
    }

    /**
     * Get array from a string data like "item1, item2"
     */
    public static function toArrayFromStringData(string $cells, string $delimiter = ','): array
    {
        $cellList = explode($delimiter, $cells);

        if (!is_array($cellList)) {
            return [];
        }

        foreach ($cellList as &$cell) {
            $cell = trim($cell);
        }

        return $cellList;
    }
}
