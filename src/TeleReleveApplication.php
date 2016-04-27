<?php

/**
 * This file is part of Mactronique EDF TeleReleve package.
 *
 * @author Jean-Baptiste Nahan <jbnahan@gmail.com>
 * @copyright 2016 - Jean-Baptiste Nahan
 * @license MIT
 */

namespace Mactronique\TeleReleve;

use Symfony\Component\Console\Application;
use Mactronique\TeleReleve\Command\ReadCommand;
use Mactronique\TeleReleve\Command\TestCommand;
use Mactronique\TeleReleve\Configuration\MainConfiguration;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Yaml\Yaml;

class TeleReleveApplication extends Application
{
    private $config;

    private $compteur;

    public function __construct()
    {
        parent::__construct('Mactronique EDF Telereleve Reader', '0.1');
        $this->add(new ReadCommand());
        $this->add(new TestCommand());
    }

    /**
     * Runs the current application.
     *
     * @param InputInterface  $input  An Input instance
     * @param OutputInterface $output An Output instance
     *
     * @return int 0 if everything went fine, or an error code
     *
     * @throws \Exception When doRun returns Exception
     */
    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        if (null === $input) {
            $input = new ArgvInput();
        }

        if (null === $output) {
            $output = new ConsoleOutput();
        }

        $this->configureIO($input, $output);

        try {
            $this->boot($input);
        } catch (\Exception $e) {
            if ($output instanceof ConsoleOutputInterface) {
                $this->renderException($e, $output->getErrorOutput());
            } else {
                $this->renderException($e, $output);
            }

            $exitCode = $e->getCode();
            if (is_numeric($exitCode)) {
                $exitCode = (int) $exitCode;
                if (0 === $exitCode) {
                    $exitCode = 1;
                }
            } else {
                $exitCode = 1;
            }
            exit($exitCode);
        }

        return parent::run($input, $output);
    }

    /**
     * @return \Mactronique\TeleReleve\Compteur\CompteurInterface
     */
    public function compteur()
    {
        return $this->compteur;
    }


    /**
     * Gets the default input definition.
     *
     * @return InputDefinition An InputDefinition instance
     */
    protected function getDefaultInputDefinition()
    {
        $input = parent::getDefaultInputDefinition();
        //$input->addOption(new InputOption('--no-config', null, InputOption::VALUE_NONE, 'Do not load the configuration'));

        return $input;
    }

    /**
     * This function run the first level booting.
     */
    private function boot(InputInterface $input)
    {
        $configFile = dirname(__DIR__).'/config.yml';
        $this->loadConfigurationFile($configFile);

        $this->loadCompteur();
    }

    /**
     * Load the configuration file.
     */
    private function loadConfigurationFile($configFile)
    {
        if (!file_exists($configFile)) {
            throw new \Exception('Le fichier de configuration ('.$configFile.') est absent ! ', 123);
        }

        $config = Yaml::parse(file_get_contents($configFile));

        $configs = [$config];
        $processor = new Processor();
        $configuration = new MainConfiguration();
        $this->config = $processor->processConfiguration($configuration, $configs);
    }

    private function loadCompteur()
    {
        $compteurClass  = 'Mactronique\TeleReleve\Compteur\Compteur'.$this->config['compteur'];
        if (!class_exists($compteurClass)) {
            throw new \LogicException("The class does not exists : ".$compteurClass, 1);
        }
        $this->compteur = $compteurClass::makeFromDevicePath($this->config['device']);
    }
}