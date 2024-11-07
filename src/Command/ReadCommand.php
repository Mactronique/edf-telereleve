<?php

declare(strict_types=1);

/**
 * This file is part of Mactronique EDF TeleReleve package.
 *
 * @author Jean-Baptiste Nahan <814683+macintoshplus@users.noreply.github.com>
 * @copyright 2016,2024 - Jean-Baptiste Nahan
 * @license MIT
 */

namespace Mactronique\TeleReleve\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ReadCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('read')
            ->setDescription('Read the power counter information and store it')
            ->setHelp(
                <<<EOH
Open the configured serial port and read the information. If the read is success, the readed data is savec into the configured storage.

EOH
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $releve = $this->getApplication()->compteur()->read();
        $this->getApplication()->storage()->save($releve);

        return self::SUCCESS;
    }
}
