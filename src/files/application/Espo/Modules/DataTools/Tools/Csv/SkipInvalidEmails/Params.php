<?php
/************************************************************************
 * This file is part of Import Tools extension for EspoCRM.
 *
 * Import Tools extension for EspoCRM.
 * Copyright (C) 2014-2023 Yurii Kuznietsov, Taras Machyshyn, Oleksii Avramenko
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

namespace Espo\Modules\DataTools\Tools\Csv\SkipInvalidEmails;

use Espo\Modules\DataTools\{
    Tools\Params as IParams,
    Utils\Csv as CsvUtils,
};

use RuntimeException;

class Params implements IParams
{
    private $toolName = 'SkipInvalidEmails';

    private $src = 'data/src.csv';

    private $dest = 'data/dest.csv';

    private $invalidDest = 'data/invalid-dest.csv';

    private $delimiter = ',';

    private $cellList = [];

    public static function create(): self
    {
        return new self();
    }

    public static function fromRaw(array $params): self
    {
        $obj = new self();

        $src = $params['src'] ?? null;
        $dest = $params['dest'] ?? null;
        $delimiter = $params['delimiter'] ?? null;
        $invalidDest = $params['invalidDest'] ?? null;
        $cells = $params['cells'] ?? null;

        if (!file_exists($src)) {
            throw new RuntimeException('Scr file "' . $src . '" is not found.');
        }

        if ($src) {
            $obj->src = $src;
        }

        if ($dest) {
            $obj->dest = $dest;
        }

        if ($delimiter) {
            $obj->delimiter = $delimiter;
        }

        if ($invalidDest) {
            $obj->delimiterInsideCell = $invalidDest;
        }

        if (!$cells) {
            throw new RuntimeException('Option "cells" is not defined.');
        }

        $obj->cellList = CsvUtils::toArrayFromStringData($cells);

        if (empty($obj->cellList)) {
            throw new RuntimeException('Incorrect cells: "' . $cells . '".');
        }

        return $obj;
    }

    public function withSrc(string $src): self
    {
        if (!file_exists($src)) {
            throw new RuntimeException('Scr file "' . $src . '" is not found.');
        }

        $obj = clone $this;

        $obj->src = $src;

        return $obj;
    }

    public function withDest(string $dest): self
    {
        $obj = clone $this;

        $obj->dest = $dest;

        return $obj;
    }

    public function withInvalidDest(string $invalidDest): self
    {
        $obj = clone $this;

        $obj->invalidDest = $invalidDest;

        return $obj;
    }

    public function withDelimiter(string $delimiter): self
    {
        $obj = clone $this;

        $obj->delimiter = $delimiter;

        return $obj;
    }

    public function withCells(string $cells): self
    {
        $obj = clone $this;

        $obj->cellList = CsvUtils::toArrayFromStringData($cells);

        return $obj;
    }

    public function withCellList(array $cellList): self
    {
        $obj = clone $this;

        $obj->cellList = $cellList;

        return $obj;
    }

    /**
     * Get a tool name
     */
    public function getToolName(): ?string
    {
        return $this->toolName;
    }

    /**
     * Get a source of csv file
     */
    public function getSrc(): ?string
    {
        return $this->src;
    }

    /**
     * Get a desination of csv file
     */
    public function getDest(): ?string
    {
        return $this->dest;
    }

    /**
     * Get a desination of invalid csv file
     */
    public function getInvalidDest(): ?string
    {
        return $this->invalidDest;
    }

    /**
     * Get a delimiter of csv file
     */
    public function getDelimiter(): ?string
    {
        return $this->delimiter;
    }

    /**
     * Get list of cells which need to be corrected
     */
    public function getCellList(): array
    {
        return $this->cellList;
    }

    public function getRaw(): array
    {
        return [
            'src' => $this->src,
            'dest' => $this->dest,
            'invalidDest' => $this->invalidDest,
            'delimiter' => $this->delimiter,
            'cellList' => $this->cellList,
        ];
    }
}
