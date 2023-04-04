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

namespace Espo\Modules\DataTools\Tools\Database\DeletedColums;

use Espo\Core\{
    Console\IO,
    Console\Command,
    Console\Command\Params,
};

use Espo\Modules\DataTools\Tools\{
    ToolRunner,
    Database\DeletedColums\Params as ToolParams,
};

class Runner implements Command
{
    private $toolRunner;

    public function __construct(ToolRunner $toolRunner)
    {
        $this->toolRunner = $toolRunner;
    }

    public function run(Params $params, IO $io): void
    {
        $toolParams = ToolParams::fromRaw([
            'dest' => $params->getOption('dest'),
            'delimiter' => $params->getOption('delimiter'),
            'entityTypes' => $params->getOption('entityTypes'),
        ]);

        $this->toolRunner->run($toolParams, $io);
    }
}
