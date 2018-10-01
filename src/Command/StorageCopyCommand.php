<?php

/**
 * This file is part of Mactronique EDF TeleReleve package.
 *
 * @author Jean-Baptiste Nahan <jbnahan@gmail.com>
 * @copyright 2016 - Jean-Baptiste Nahan
 * @license MIT
 */
namespace Mactronique\TeleReleve\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StorageCopyCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('storage:copy')
            ->setDefinition([
                new InputArgument('from', InputArgument::REQUIRED, 'The key for source storage for read data'),
                new InputArgument('to', InputArgument::REQUIRED, 'The key of destination storage for writing data'),
                new InputArgument('start', InputArgument::OPTIONAL, 'The date of the first day to be copied', date('Y-m-d')),
                new InputArgument('count', InputArgument::OPTIONAL, 'Day number to be copied', 1),

            ])
            ->setDescription('This command copy the storage data into another storage')
            ->setHelp(<<<EOH
This command copy the datas information saved into the storage "from" into the "to" storage.

<comment>Work only if the storage configuration use the "Chain" storage with two storages.</comment>

Exemple: %command.full_name% sqlite influx 2018-06-06 2

This exemple copy the datas for two days (the June 6 2018 and the June 7 2018) from "sqlite" storage to the "influx" storage.

EOH
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $storageChain = $this->getApplication()->storage();
        if (!$storageChain instanceof \Mactronique\TeleReleve\Storage\StorageChain) {
            throw new \Exception("Unable to copy data from storage to another storage if the main storage is not Chain.", 500);
        }
        $output->writeln("Storage keys defined : <info>".implode(', ', $storageChain->storageLoaded())."</info>");

        $output->writeln("Source storage : <info>".$input->getArgument('from')."</info>");
        $output->writeln("Destination storage : <info>".$input->getArgument('to')."</info>");
        $output->writeln("The date of the first day to be copied : <info>".$input->getArgument('start')."</info>");
        $output->writeln("Day number to be copied : <info>".$input->getArgument('count')."</info>");

        $storageFrom = $storageChain->storageAtKey($input->getArgument('from'));


        $storageTo = $storageChain->storageAtKey($input->getArgument('to'));

        $date = $input->getArgument('start');
        $daysCount = $input->getArgument('count');

        $d = \DateTime::createFromFormat('Y-m-d', $date);
        for ($day = 0; $day < $daysCount; $day++) {

            $output->write("<comment>Copy ".$d->format('Y-m-d')."</comment> ");
            $datas = $storageFrom->read($d->format('Y-m-d'));
            $output->writeln("<info>releve count ".count($datas)."</info> ");
            foreach ($datas as $key => $data) {
                $data = array_change_key_case($data, CASE_UPPER);
                $releve = \Mactronique\TeleReleve\Compteur\Releve::makeFromStorage('CBEMM', $data);
                $storageTo->save($releve);
            }
            $d = $d->add(new \DateInterval('P1D'));
        }

        $output->writeln("End of copy");
    }
}
