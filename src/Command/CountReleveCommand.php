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
use Symfony\Component\Console\Helper\TableHelper;

class CountReleveCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('count')
            ->setDefinition([
                new InputOption('date', null, InputOption::VALUE_REQUIRED, 'The date at you want count and summarize.'),
                new InputOption('send-email', null, InputOption::VALUE_NONE, 'Send by email. If not set throw exception.'),
            ])
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $date = date('Y-m-d');
        if ($input->getOption('date') !== null) {
            $date = $input->getOption('date');
        }

        $output->writeln('Read count for <info>'.$date.'</info>: ');
        $data = $this->getApplication()->storage()->read($date);
        $nb = count($data);
        $output->writeln('Row count : <info>'.$nb.'</info>');

        $table_data = [['', '', '', ''], ['', '', '', '']];
        $conso_totale = "";
        if ($nb>0) {
            $first = $data[0];
            $last = end($data);
            $hchc = ($last['hchc'] - $first['hchc'])/1000;
            $hchp = ($last['hchp'] - $first['hchp'])/1000;

            $table_data[0] = [
                'Heures creuses',
                sprintf('%10s', number_format($first['hchc']/1000, 0, ',', ' ')),
                sprintf('%10s', number_format($last['hchc']/1000, 0, ',', ' ')),
                sprintf('%10s', number_format($hchc, 3, ',', ' ')),
            ];
            $table_data[1] = [
                'Heures pleines',
                sprintf('%10s', number_format($first['hchp']/1000, 0, ',', ' ')),
                sprintf('%10s', number_format($last['hchp']/1000, 0, ',', ' ')),
                sprintf('%10s', number_format($hchp, 3, ',', ' ')),
            ];

            $conso_totale = number_format($hchc + $hchp, 3, ',', ' ');
            //dump($first);
            //dump($last);
            $table = new TableHelper();
            $table->setHeaders(['Pricing', 'Start index (Kwh)', 'Last index (Kwh)', 'Delta (Kwh)']);
            $table->setRows($table_data);
            $table->render($output);

            $output->writeln("Total : <info>".$conso_totale."</info> Kwh");
        }
        //dump($hchc);
        //dump($hchp);


        //return;
        if ($input->getOption('send-email')) {
            $this->getApplication()->sendMessage(
                'TeleReleve count for '.$date,
                sprintf(
                    "Bonjour,\nVoici le décompte du nombre de télérelevé réalisé le %s\nIl y a eu %d relevé(s).\n
Période      : %s %s
Heure creuse : %s %s Soit un total de %s Kwh
Heure pleine : %s %s Soit un total de %s Kwh
Consommation total : %s Kwh
\n\nCordialement.",
                    $date,
                    $nb,
                    'Début',
                    'Fin',
                    $table_data[0][1],
                    $table_data[0][2],
                    $table_data[0][3],
                    $table_data[1][1],
                    $table_data[1][2],
                    $table_data[1][3],
                    $conso_totale
                )
            );
            $output->writeln('<comment>E-mail sent !</comment>');
        }

    }
}
