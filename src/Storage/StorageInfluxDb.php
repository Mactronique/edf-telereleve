<?php

namespace Mactronique\TeleReleve\Storage;

use Mactronique\TeleReleve\Compteur\ReleveInterface;

class StorageInfluxDb implements StorageInterface
{
    private $config;

    /**
     * @param string $path the path to the SQLITE File.
     */
    public function __construct(array $config)
    {
        if (!array_key_exists('host', $config)) {
            $config['host'] = 'localhost';
        }
        if (!array_key_exists('port', $config)) {
            $config['port'] = '3386';
        }
        if (!array_key_exists('database', $config)) {
            throw new StorageException("Influx Database not set", 1);
        }
        $this->config = $config;

    }
    /**
     * Save the releve
     * @param ReleveInterface $releve
     * @return mixed
     */
    public function save(ReleveInterface $releve)
    {
        $point = new \InfluxDB\Point(
            'releve',
            null,
            [],
            [
                'ptec' => $releve->valueAtIndex('PTEC'),
                'iinst' => $releve->valueAtIndex('IINST'),
                'hchc' => $releve->valueAtIndex('HCHC'),
                'hchp' => $releve->valueAtIndex('HCHP'),
                'base' => $releve->valueAtIndex('BASE')
            ],
            $releve->at()->getTimestamp()
        );
        $db = $this->getDatabase();

        $db->writePoints([$point], \InfluxDB\Database::PRECISION_SECONDS);

    }

    public function read($at)
    {
        $database = $this->getDatabase();

        $result = $database->query("SELECT * FROM releve WHERE hchc != '' AND hchp != '' ORDER BY time DESC LIMIT 1");
        
        $datas = [];
        while ($row = $result->getPoints()) {
            $data = $row->getFields();
            $data['at'] = date('Y-m-d H:i:s', $row->getTimestamp());
            $datas[] = $data;
        }
        return $datas;
        
    }

    /**
     * Connect to DataBase and return client
     */
    private function getDatabase()
    {
        $client  = new \InfluxDB\Client($this->config['host'], $this->config['port']);

        return $client->selectDB($this->config['database']);
    }
}
