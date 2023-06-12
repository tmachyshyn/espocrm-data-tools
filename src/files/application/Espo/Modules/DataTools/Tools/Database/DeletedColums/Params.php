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

namespace Espo\Modules\DataTools\Tools\Database\DeletedColumns;

use Espo\Modules\DataTools\{
    Tools\Params as IParams,
    Utils\Csv as CsvUtils,
};

class Params implements IParams
{
    private $toolName = 'DeletedColumns';

    private $dest = 'data/dest.csv';

    private string $delimiter = ',';

    private ?array $entityTypeList = null;

    public static function create(): self
    {
        return new self();
    }

    public static function fromRaw(array $params): self
    {
        $obj = new self();

        $dest = $params['dest'] ?? null;
        $delimiter = $params['delimiter'] ?? ',';
        $entityTypes = $params['entityTypes'] ?? null;

        if ($dest) {
            $obj->dest = $dest;
        }

        if ($delimiter) {
            $obj->delimiter = $delimiter;
        }

        if ($entityTypes) {
            $obj->entityTypeList = CsvUtils::toArrayFromStringData($entityTypes);
        }

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

    public function withEntityTypes(string $entityTypes): self
    {
        $obj = clone $this;

        $obj->entityTypeList = CsvUtils::toArrayFromStringData($entityTypes);

        return $obj;
    }

    public function withEntityTypeList(array $entityTypeList): self
    {
        $obj = clone $this;

        $obj->entityTypeList = $entityTypeList;

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
     * Get list of entity
     */
    public function getEntityTypeList(): ?array
    {
        return $this->entityTypeList;
    }

    public function getRaw(): array
    {
        return [
            'dest' => $this->dest,
            'delimiter' => $this->delimiter,
            'entityTypeList' => $this->entityTypeList,
        ];
    }
}
