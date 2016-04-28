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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\ProcessTimedOutException;

class ReadCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('read')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $releve = $this->getApplication()->compteur()->read();
        $this->getApplication()->storage()->save($releve);
    }
}
