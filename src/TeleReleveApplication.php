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
use Mactronique\TeleReleve\Command\CountReleveCommand;
use Mactronique\TeleReleve\Configuration\MainConfiguration;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Yaml\Yaml;
use Mactronique\TeleReleve\Storage\StorageInterface;
use Swift_SmtpTransport;
use Swift_Mailer;
use Swift_Message;

class TeleReleveApplication extends Application
{
    private $config;

    /**
     * @var \Mactronique\TeleReleve\Compteur\CompteurInterface
     */
    private $compteur;

    /**
     * @var \Mactronique\TeleReleve\Storage\StorageInterface
     */
    private $storage;

    public function __construct()
    {
        parent::__construct('Mactronique EDF Telereleve Reader', '0.1');
        $this->add(new ReadCommand());
        $this->add(new TestCommand());
        $this->add(new CountReleveCommand());
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
     * Gets the value of storage.
     *
     * @return \Mactronique\TeleReleve\Storage\StorageInterface
     */
    public function storage()
    {
        return $this->storage;
    }

    /**
     * Sent email
     */
    public function sendMessage($subject, $body)
    {
        if (!$this->config['enable_email']) {
            throw new Exception("Email sending is not enabled", 1);
        }

        $transport = Swift_SmtpTransport::newInstance($this->config['smtp']['server'], $this->config['smtp']['port'], $this->config['smtp']['security'])
            ->setUsername($this->config['smtp']['username'])
            ->setPassword($this->config['smtp']['password']);
        $mailer = Swift_Mailer::newInstance($transport);
        $message = Swift_Message::newInstance($subject)
          ->setFrom([$this->config['smtp']['from']['email']=> $this->config['smtp']['from']['display_name']])
          ->setTo([$this->config['smtp']['to']['email']=> $this->config['smtp']['to']['display_name']])
          ->setBody($body)
          ;
        //dump($message->getBody());
        //return;

        // Send the message
        $result = $mailer->send($message);
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
        $this->loadStorage();
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

    private function loadStorage()
    {
        $storageClass  = 'Mactronique\TeleReleve\Storage\Storage'.$this->config['storage']['driver'];
        if (!class_exists($storageClass)) {
            throw new \LogicException("The class does not exists : ".$storageClass, 1);
        }
        
        $this->storage = new $storageClass($this->config['storage']['parameters']);
    }
}
