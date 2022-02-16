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

use Espo\Modules\ImportTools\Tools\Params as IParams;

use RuntimeException;

class Params implements IParams
{
    private $toolName = 'FixCellData';

    private $src = 'data/src.csv';

    private $dest = 'data/dest.csv';

    private $delimiter = ',';

    private $delimiterInsideCell = ',';

    private $cellList = [];

    public static function create(): self
    {
        return new self();
    }

    public static function fromRaw(array $params): self
    {
        $obj = new self();

        if ($params['src']) {
            $obj->src = $params['src'];
        }

        if ($params['dest']) {
            $obj->dest = $params['dest'];
        }

        if ($params['delimiter']) {
            $obj->delimiter = $params['delimiter'];
        }

        if ($params['delimiterInsideCell']) {
            $obj->delimiterInsideCell = $params['delimiterInsideCell'];
        }

        $cells = $params['cells'] ?? null;

        if (!$cells) {
            throw new RuntimeException('Option "cells" is not defined.');
        }

        $obj->cellList = $obj->normalizeCells($cells);

        return $obj;
    }

    public function withSrc(string $src): self
    {
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

    public function withDelimiter(string $delimiter): self
    {
        $obj = clone $this;

        $obj->delimiter = $delimiter;

        return $obj;
    }

    public function withDelimiterInsideCell(string $delimiterInsideCell): self
    {
        $obj = clone $this;

        $obj->delimiterInsideCell = $delimiterInsideCell;

        return $obj;
    }

    public function withCells(string $cells): self
    {
        $obj = clone $this;

        $obj->cellList = $obj->normalizeCells($cells);

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
     * Get a delimiter of csv file
     */
    public function getDelimiter(): ?string
    {
        return $this->delimiter;
    }

    /**
     * Get a delimiter inside a cell
     */
    public function getDelimiterInsideCell(): ?string
    {
        return $this->delimiterInsideCell;
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
            'delimiter' => $this->delimiter,
            'delimiterInsideCell' => $this->delimiterInsideCell,
            'cellList' => $this->cellList,
        ];
    }

    private function normalizeCells(string $cells): array
    {
        $cellList = explode(',', $cells);

        if (!is_array($cellList)) {
            throw new RuntimeException('Option "cells" is not defined.');
        }

        foreach ($cellList as &$cell) {
            $cell = trim($cell);
        }

        return $cellList;
    }
}
