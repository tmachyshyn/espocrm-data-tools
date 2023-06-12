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

use Espo\{
    Core\Di,
    ORM\Defs,
    Core\Utils\Util,
    Core\Exceptions\Error,
};

use Espo\Modules\DataTools\{
    Tools\Params,
    Tools\Tool as ITool,
};

class Tool implements

    ITool,
    Di\MetadataAware,
    Di\EntityManagerAware
{
    use Di\MetadataSetter;
    use Di\EntityManagerSetter;

    private Defs $defs;

    public function __construct(Defs $defs)
    {
        $this->defs = $defs;
    }

    public function run(Params $params): void
    {
        $dest = $params->getDest();
        $delimiter = $params->getDelimiter();
        $headerList = $this->getHeaderList();
        $entityTypeList = $params->getEntityTypeList() ?? $this->getDefaultEntityTypeList();

        if (file_exists($dest)) {
            unlink($dest);
        }

        $fp = fopen($dest, 'w');

        if (!$fp) {
            throw new Error('Unable to create a file "' . $dest . '".');
        }

        fputcsv($fp, $headerList, $delimiter);

        foreach ($entityTypeList as $entityType) {
            $columnData = $this->getColumnData($entityType);

            if (empty($columnData)) {

                continue;
            }

            $entity = $this->entityManager->getNewEntity($entityType);

            foreach ($columnData as $data) {
                if ($entity->hasAttribute($data['fieldName'])) {

                    continue;
                }

                fputcsv($fp, [
                    $entityType,
                    $data['fieldName'],
                    $data['columnName'],
                    $data['columnType'] ?? null,
                    $data['sql'] ?? null,
                ], $delimiter);
            }
        }

        rewind($fp);
    }

    private function getHeaderList(): array
    {
        return [
            'Entity Type',
            'Field Name',
            'Column Name in Database',
            'Column Type in Database',
            'SQL',
        ];
    }

    private function getDefaultEntityTypeList(): array
    {
        $defaultList = $this->defs->getEntityTypeList();

        $list = [];

        foreach ($defaultList as $entityType) {
            if (!$this->metadata->get(['scopes', $entityType, 'customizable'])) {

                continue;
            }

            $list[] = $entityType;
        }

        return $list;
    }

    private function getColumnData($entityType): array
    {
        $tableName = Util::toUnderScore($entityType);

        $sth = $this->entityManager->getPDO()->query(
            "SHOW FULL COLUMNS FROM `" . $tableName . "`"
        );

        $rows = $sth->fetchAll();

        $data = [];

        foreach ($rows as $row) {
            $columnName = $row['Field'] ?? null;
            $columnType = $row['Type'] ?? null;

            if (!$columnName) {

                continue;
            }

            $data[] = [
                'columnName' => $columnName,
                'columnType' => $columnType,
                'fieldName' => Util::toCamelCase($columnName),
                'sql' => "ALTER TABLE `" . $tableName . "` DROP `" . $columnName . "`;",
            ];
        }

        return $data;
    }
}
