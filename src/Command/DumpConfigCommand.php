<?php
/**
 * @copyright Macintoshplus (c) 2018
 * Added by : Macintoshplus at 11/07/18 21:18
 */

namespace Mactronique\TeleReleve\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class DumpConfigCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('dump:config')

            ->setDescription('This display the current configuration.')
            ->setHelp(<<<EOH
Display the current configuration in Yaml format with default values and values defined into the 'config.yml' configuration file.

EOH
            )

        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>The current configuration:</info>');
        $output->writeln(Yaml::dump($this->getApplication()->getConfig(), 10));
    }
}
