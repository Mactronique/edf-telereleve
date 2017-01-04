<?php

namespace Mactronique\TeleReleve\Storage;

use Mactronique\TeleReleve\Compteur\ReleveInterface;
use Psr\Log\LoggerAwareTrait;

class StorageChain implements StorageInterface
{
    use LoggerAwareTrait;
    /**
     * @var array Configurations
     */
    private $configs;

    /**
     * @var array Objects for each storage
     */
    private $storages;

    /**
     * @param array $configs array of configuration for each storage component
     */
    public function __construct(array $configs)
    {
        if (!isset($configs['storages'])) {
            throw new \Exception("Unable to finds the 'storages' list settings", 500);
        }
        if (!isset($configs['skip_on_storage_error'])) {
            $configs['skip_on_storage_error'] = false;
        } else {
            $configs['skip_on_storage_error'] = boolval($configs['skip_on_storage_error']);
        }

        $this->configs = $configs;
        $this->storages = [];

        $this->loadStorage();

        $this->logger = new \Psr\Log\NullLogger();
        
    }

    /**
     * Save the releve
     * @param ReleveInterface $releve
     * @return mixed
     */
    public function save(ReleveInterface $releve)
    {
        foreach ($this->storages as $key => $storage) {
            try {
                $storage->save($releve);
            } catch (\Throwable $e) {
                $this->logger->error('ChainStorage : Error on save', ['storage'=>$key, 'releve'=>['at'=>$releve->at()->format('c'), 'datas'=>$releve->index()], 'exception'=>$e]);
                if (!$this->configs['skip_on_storage_error']) {
                    throw new StorageException(sprintf('Error or exception on storage %s', $key), $e->getCode(), $e);
                }
            }
        }
    }


    /**
     * Return the array of configuration
     * @return array
     */
    public function configuration()
    {
        return $this->configs;
    }

    /**
     * Init all configured storage
     */
    private function loadStorage()
    {
        foreach ($this->configs['storages'] as $key => $config) {
            if (!isset($config['driver'])) {
                throw new \Exception(sprintf("Unable to find the driver name on storage configuration %s", $key), 500);
            }

            if (!isset($config['parameters'])) {
                throw new \Exception(sprintf("Unable to find the driver parameters on storage configuration %s", $key), 500);
            }

            $storageClass  = 'Mactronique\TeleReleve\Storage\Storage'.$config['driver'];
            if (!class_exists($storageClass)) {
                throw new \LogicException("The class does not exists : ".$storageClass, 500);
            }

            if ($storageClass == __CLASS__) {
                throw new \LogicException("Unable to load the Chain storage into an chain storage.", 500);
            }

            $this->storages[$key] = new $storageClass($config['parameters']);
        }
        
    }
}
