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

namespace Espo\Modules\DataTools\Tools;

use Espo\Core\{
    Di,
    Console\IO,
};

use Espo\Modules\DataTools\Tools\Params as ToolParams;

use Throwable;

class ToolRunner implements

    Di\MetadataAware,
    Di\InjectableFactoryAware
{
    use Di\MetadataSetter;
    use Di\InjectableFactorySetter;

    public function run(ToolParams $toolParams, IO $io): bool
    {
        $className = $this->metadata->get([
            'app', 'dataTools', 'toolsClassNameMap', $toolParams->getToolName()
        ]);

        if (!$className || !class_exists($className)) {
            $io->writeLine(
                "Error: tool is not found."
            );

            return false;
        }

        $class = $this->injectableFactory->create($className);

        try {
            $class->run($toolParams);
        } catch (Throwable $e) {
            $io->writeLine(
                "Error: " . $e->getMessage()
            );

            $GLOBALS['log']->error(
                'DataTools Error: ' . $e->getMessage() .
                ' at '. $e->getFile() . ':' . $e->getLine()
            );

            return false;
        }

        $io->writeLine(
            "Done. Saved to \"" . $toolParams->getDest() . "\"."
        );

        return true;
    }
}
