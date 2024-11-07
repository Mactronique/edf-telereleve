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
use Symfony\Component\Yaml\Yaml;

class DumpStorageCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('dump:storage')
            ->setDescription('Dump the storage configuration.')
            ->setHelp(
                <<<EOH
This command dump the current storage configuration in YAML format.

EOH
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $storage = $this->getApplication()->storage();
        $mainStorageClassName = $storage::class;
        $output->writeln('The main class for storage is : <info>'.$mainStorageClassName.'</info>');

        $yaml = Yaml::dump($storage->configuration(), 10, 4);
        $output->writeln('The configuration for storage is :');
        $output->writeln($yaml);

        return self::SUCCESS;
    }
}
