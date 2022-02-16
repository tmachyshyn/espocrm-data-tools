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

namespace Espo\Modules\ImportTools\Tools\Csv\SkipInvalidEmails;

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
        $this->convertData($params);
    }

    private function convertData(Params $params): void
    {
        $src = $params->getSrc();
        $dest = $params->getDest();
        $invalidDest = $params->getInvalidDest();
        $delimiter = $params->getDelimiter();
        $cellList = $params->getCellList();

        if (file_exists($dest)) {
            unlink($dest);
        }

        $fpSrc = fopen($src, "r");
        $fpDest = fopen($dest, 'w');
        $fpInvalid = fopen($invalidDest, 'w');

        if (!$fpDest) {
            throw new Error('Unable to create a file "' . $dest . '".');
        }

        if (!$fpInvalid) {
            throw new Error('Unable to create a file "' . $invalidDest . '".');
        }

        $headerList = null;

        while (($row = fgetcsv($fpSrc, 0, $delimiter)) !== FALSE) {

            if (!$headerList) {
                $headerList = $row;
                $this->validateCellList($params, $headerList);

                fputcsv($fpDest, $headerList, $delimiter);
                fputcsv($fpInvalid, $headerList, $delimiter);

                continue;
            }

            $data = CsvUtil::toAssocFromRow($row, $headerList);

            $isEmailValid = true;

            foreach ($cellList as $cellName) {

                $emailAddress = $data[$cellName] ?? null;

                if (empty($emailAddress)) {

                    continue;
                }

                $isEmailValid &= $this->isEmailValid($emailAddress);
            }

            $fp = $isEmailValid ? $fpDest : $fpInvalid;

            fputcsv(
                $fp,
                CsvUtil::toRowFromAssoc($data, $headerList),
                $delimiter
            );
        }

        rewind($fpSrc);
        rewind($fpDest);
        rewind($fpInvalid);
    }

    private function validateCellList(Params $params, array $headerList): bool
    {
        $cellList = $params->getCellList();

        foreach ($cellList as $cellName) {
            if (!in_array($cellName, $headerList)) {
                throw new Error('Cell "' . $cellName . '" is not found in .csv.');
            }
        }

        return true;
    }

    private function isEmailValid(string $emailAddress): bool
    {
        if (filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
            return true;
        }

        return false;
    }
}
