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
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('test')

            ->setDescription('This command read the data from the serial port and display.')
            ->setHelp(
                <<<EOH
This command open and read serial data from the serial bus define into the configuration file.

After read it, the data is computed depending to the power counter type defined into the configuration file.

At end, the data is diplayed on the terminal.

EOH
            )

        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $releve = $this->getApplication()->compteur()->read();
        $table = new Table($output);
        $table->setHeaders(['Code', 'Description', 'Valeur', 'Unitée']);
        $table->setRows($releve->describe());
        $table->render();

        // dump($releve->describe());
        return self::SUCCESS;
    }
}
