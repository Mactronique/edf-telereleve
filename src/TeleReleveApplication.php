<?php

declare(strict_types=1);

/**
 * This file is part of Mactronique EDF TeleReleve package.
 *
 * @author Jean-Baptiste Nahan <814683+macintoshplus@users.noreply.github.com>
 * @copyright 2016,2024 - Jean-Baptiste Nahan
 * @license MIT
 */

namespace Mactronique\TeleReleve;

use Mactronique\TeleReleve\Command\CountReleveCommand;
use Mactronique\TeleReleve\Command\DumpConfigCommand;
use Mactronique\TeleReleve\Command\DumpStorageCommand;
use Mactronique\TeleReleve\Command\ReadCommand;
use Mactronique\TeleReleve\Command\StorageCopyCommand;
use Mactronique\TeleReleve\Command\TestCommand;
use Mactronique\TeleReleve\Compteur\CompteurInterface;
use Mactronique\TeleReleve\Configuration\MainConfiguration;
use Mactronique\TeleReleve\Storage\StorageInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Transport\Smtp\SmtpTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Yaml\Yaml;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TeleReleveApplication extends Application
{
    /**
     * @var array<int|string, mixed>
     */
    private array $config;

    private LoggerInterface $logger;

    private CompteurInterface $compteur;

    private StorageInterface $storage;

    public function __construct()
    {
        parent::__construct('Mactronique EDF Telereleve Reader', '0.5.0');
        $this->add(new ReadCommand());
        $this->add(new TestCommand());
        $this->add(new CountReleveCommand());
        $this->add(new DumpStorageCommand());
        $this->add(new StorageCopyCommand());
        $this->add(new DumpConfigCommand());
    }

    /**
     * Runs the current application.
     *
     * @throws \Exception When doRun returns Exception
     */
    public function run(?InputInterface $input = null, ?OutputInterface $output = null): int
    {
        if ($input === null) {
            $input = new ArgvInput();
        }

        if ($output === null) {
            $output = new ConsoleOutput();
        }

        $this->configureIO($input, $output);

        try {
            $this->boot($input);
        } catch (\Exception $e) {
            if ($output instanceof ConsoleOutputInterface) {
                $this->renderThrowable($e, $output->getErrorOutput());
            } else {
                $this->renderThrowable($e, $output);
            }

            $exitCode = $e->getCode();
            if (is_numeric($exitCode)) {
                $exitCode = (int) $exitCode;
                if ($exitCode === 0) {
                    $exitCode = 1;
                }
            } else {
                $exitCode = 1;
            }
            exit($exitCode);
        }

        return parent::run($input, $output);
    }

    public function logger(): LoggerInterface
    {
        return $this->logger;
    }

    public function compteur(): CompteurInterface
    {
        return $this->compteur;
    }

    public function storage(): StorageInterface
    {
        return $this->storage;
    }

    /**
     * Sent email.
     */
    public function sendMessage(string $subject, array $body): void
    {
        if (!$this->config['enable_email']) {
            throw new \Exception('Email sending is not enabled', 1);
        }

        $loader = new FilesystemLoader(__DIR__.'/Templates');
        $twig = new Environment($loader, [
            // 'cache' => '/path/to/compilation_cache',
        ]);

        $content = $twig->render($this->config['template'], $body);

        $transport = new SmtpTransport();

        if (($this->config['smtp']['username'] ?? null) !== null) {
            $transport = new EsmtpTransport();
            $transport->setUsername($this->config['smtp']['username']);
            $transport->setPassword($this->config['smtp']['password']);
        }

        $stream = $transport->getStream();
        $stream->setHost($this->config['smtp']['server']);
        $stream->setPort($this->config['smtp']['port']);
        if ($this->config['smtp']['tls'] === false) {
            $stream->disableTls();
        }

        $mailer = new Mailer($transport);

        $message = new Email();
        $message->subject($subject);
        $type = explode('/', $this->config['smtp']['mime']);

        $subtype = $type[1] ?? 'plain';
        if ($subtype === 'plain') {
            $message->text($content);
        }
        if ($subtype === 'html') {
            $message->html($content);
        }

        $message->addFrom(
            new Address(
                $this->config['smtp']['from']['email'],
                $this->config['smtp']['from']['display_name']
            )
        );
        $message->addTo(new Address($this->config['smtp']['to']['email'], $this->config['smtp']['to']['display_name']));

        $mailer->send($message);
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    /*
     * This function run the first level booting.
     */
    private function boot(InputInterface $input): void
    {
        $configFile = \dirname(__DIR__).'/config.yml';
        $this->loadConfigurationFile($configFile);
        $this->loadLogger();
        $this->loadCompteur();
        $this->loadStorage();
    }

    /*
     * Load the configuration file.
     */
    private function loadConfigurationFile($configFile): void
    {
        if (!file_exists($configFile)) {
            throw new \Exception('The configuration file ('.$configFile.') is not found ! ', 123);
        }

        $config = Yaml::parse(file_get_contents($configFile));

        $configs = [$config];
        $processor = new Processor();
        $configuration = new MainConfiguration();
        $this->config = $processor->processConfiguration($configuration, $configs);
    }

    private function loadLogger(): void
    {
        $file = $this->config['log_file'];

        $this->logger = new Logger('main');
        $this->logger->pushHandler(new StreamHandler($file));
    }

    private function loadCompteur(): void
    {
        $compteurClass = 'Mactronique\TeleReleve\Compteur\Compteur'.$this->config['compteur'];
        if (!class_exists($compteurClass)) {
            throw new \LogicException('The class does not exists : '.$compteurClass, 1);
        }
        $this->compteur = $compteurClass::makeFromDevicePath($this->config['device']);
    }

    private function loadStorage(): void
    {
        $storageClass = 'Mactronique\TeleReleve\Storage\Storage'.$this->config['storage']['driver'];
        if (!class_exists($storageClass)) {
            throw new \LogicException('The class does not exists : '.$storageClass, 1);
        }

        $this->storage = new $storageClass($this->config['storage']['parameters']);
        $this->storage->setLogger($this->logger());
    }
}
