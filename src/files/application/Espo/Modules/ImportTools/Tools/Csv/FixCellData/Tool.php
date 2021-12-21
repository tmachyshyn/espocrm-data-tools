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

namespace Espo\Modules\ImportTools\Tools\Csv\FixCellData;

use Espo\Modules\ImportTools\{
    Tools\Params,
    Tools\Tool as ITool,
    Utils\Csv as CsvUtil
};

use Espo\Core\Exceptions\Error;

class Tool implements ITool
{
    public function run(Params $params) : void
    {
        $src = $params->getSrc();

        if (!file_exists($src)) {
            throw new Error('Src file is not found.');
        }

        if (!$params->getCellList()) {
            throw new Error('Cells is not defined.');
        }

        $convertedHeaderList = $this->generateHeaderList($params);

        if (empty($convertedHeaderList)) {
            throw new Error('Header is required in .csv.');
        }

        $this->convertData($params, $convertedHeaderList);
    }

    private function generateHeaderList(Params $params): array
    {
        $src = $params->getSrc();
        $delimiter = $params->getDelimiter();
        $cellList = $params->getCellList();
        $delimiterInsideCell = $params->getDelimiterInsideCell();

        $fp = fopen($src, "r");

        $headerList = null;
        $convertedHeaderList = [];

        while (($row = fgetcsv($fp, 0, $delimiter)) !== FALSE) {

            if (!$headerList) {
                $headerList = $row;
                $convertedHeaderList = $headerList;

                continue;
            }

            $data = CsvUtil::toAssocFromRow($row, $headerList);

            foreach ($cellList as $cellName) {

                if (!array_key_exists($cellName, $data)) {

                    continue;
                }

                $cellData = $this->normalizeCellData(
                    $data[$cellName], $delimiterInsideCell
                );

                if (!is_array($cellData) || count($cellData) < 2) {

                    continue;
                }

                for ($i=1; $i < count($cellData); $i++) {
                    $headerName = $cellName . ($i + 1);

                    if (!in_array($headerName, $convertedHeaderList)) {
                        $convertedHeaderList[] = $headerName;
                    }
                }
            }
        }

        fclose($fp);

        return $convertedHeaderList;
    }

    private function convertData(Params $params, array $convertedHeaderList): void
    {
        $src = $params->getSrc();
        $dest = $params->getDest();
        $delimiter = $params->getDelimiter();
        $cellList = $params->getCellList();
        $delimiterInsideCell = $params->getDelimiterInsideCell();

        if (file_exists($dest)) {
            unlink($dest);
        }

        $fp = fopen($src, "r");
        $fpConverted = fopen($dest, 'w');

        if (!$fpConverted) {
            throw new Error('Unable to create a file "' . $dest . '".');
        }

        fputcsv($fpConverted, $convertedHeaderList, $delimiter);

        $headerList = null;

        while (($row = fgetcsv($fp, 0, $delimiter)) !== FALSE) {

            if (!$headerList) {
                $headerList = $row;
                continue;
            }

            $data = CsvUtil::toAssocFromRow($row, $headerList);

            foreach ($cellList as $cellName) {

                if (!array_key_exists($cellName, $data)) {

                    continue;
                }

                $cellData = $this->normalizeCellData(
                    $data[$cellName], $delimiterInsideCell
                );

                if (!is_array($cellData)) {

                    continue;
                }

                $data[$cellName] = $cellData[0];

                for ($i=1; $i < count($cellData); $i++) {
                    $headerName = $cellName . ($i + 1);
                    $data[$headerName] = $cellData[$i];
                }
            }

            fputcsv(
                $fpConverted,
                CsvUtil::toRowFromAssoc($data, $convertedHeaderList),
                $delimiter
            );
        }

        rewind($fp);
        rewind($fpConverted);
    }

    private function normalizeCellData(string $cell, string $delimiterInsideCell): array
    {
        $cellList = explode($delimiterInsideCell, $cell);

        if (!is_array($cellList)) {
            return $cell;
        }

        foreach ($cellList as &$cellData) {
            $cellData = trim($cellData);
        }

        return $cellList;
    }
}
