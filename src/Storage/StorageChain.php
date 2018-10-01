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
     * @throws \Exception
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
     * @return void
     * @throws StorageException
     */
    public function save(ReleveInterface $releve)
    {
        foreach ($this->storages as $key => $storage) {
            try {
                $storage->save($releve);
            } catch (\Exception $e) {
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
     * @param string $at
     * @return array
     */
    public function read($at)
    {
        reset($this->storages);
        $storage = current($this->storages);
        return $storage->read($at);
    }

    /**
     * Return an array with all storage key
     * @return array
     */
    public function storageLoaded()
    {
        return array_keys($this->storages);
    }

    /**
     * Return the storage for the key
     * @param string $key
     * @return StorageInterface
     * @throws StorageException if not found
     */
    public function storageAtKey($key)
    {
        if (!array_key_exists($key, $this->storages)) {
            throw new StorageException(sprintf("No storage found for this key %s", $key), 500);
        }
        return $this->storages[$key];
    }

    /**
     * Init all configured storage
     * @throws StorageException
     */
    private function loadStorage()
    {
        foreach ($this->configs['storages'] as $key => $config) {
            if (!isset($config['driver'])) {
                throw new StorageException(sprintf("Unable to find the driver name on storage configuration %s", $key), 500);
            }

            if (!isset($config['parameters'])) {
                throw new StorageException(sprintf("Unable to find the driver parameters on storage configuration %s", $key), 500);
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
