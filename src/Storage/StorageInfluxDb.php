<?php

namespace Mactronique\TeleReleve\Storage;

use Mactronique\TeleReleve\Compteur\ReleveInterface;
use Psr\Log\LoggerAwareTrait;

class StorageInfluxDb implements StorageInterface
{
    use LoggerAwareTrait;

    /**
     * @var array Configuration pour la connexion au serveur InfluxDB
     */
    private $config;

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
        $this->logger = new \Psr\Log\NullLogger();

    }

    /**
     * Save the releve
     * @param ReleveInterface $releve
     * @return void
     * @throws \InfluxDB\Exception
     */
    public function save(ReleveInterface $releve)
    {

	if ($this->getIndexOrZero($releve, 'PTEC') === 0) {
		return;
	}

        $point = new \InfluxDB\Point(
            'releve',
            null,
            [
                'ptec' => $this->getIndexOrZero($releve, 'PTEC'),
            ],
            [
                'iinst' => floatval($this->getIndexOrZero($releve, 'IINST')),
                'hchc' => floatval($this->getIndexOrZero($releve, 'HCHC')),
                'hchp' => floatval($this->getIndexOrZero($releve, 'HCHP')),
                'base' => floatval($this->getIndexOrZero($releve, 'BASE')),
            ],
            $releve->at()->getTimestamp()
        );
//        dump($point);
        $db = $this->getDatabase();

        $db->writePoints([$point], \InfluxDB\Database::PRECISION_SECONDS);

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
     * @throws \Exception
     */
    public function read($at)
    {
        $database = $this->getDatabase();
        $result = $database->query(sprintf(
            "SELECT * FROM releve WHERE hchc > 0 AND hchp > 0 AND time > '%s 00:00:00' and time < '%s 23:59:59' ORDER BY time ASC",
            $at,
            $at
        ));

        $datas = [];
        $points = $result->getPoints();
        foreach ($points as $row) {
            $data = $row;
            $data['at'] = $data['time'];
            unset($data['time']);
            $datas[] = $data;
        }
        return $datas;
    }

    /**
     * Connect to DataBase and return client
     */
    private function getDatabase()
    {
        $client  = new \InfluxDB\Client(
            $this->config['host'],
            $this->config['port'],
            isset($this->config['user'])? $this->config['user']:null,
            isset($this->config['user'])? $this->config['password']:null
        );

        return $client->selectDB($this->config['database']);
    }

    /**
     * @param ReleveInterface $releve
     * @param $index
     * @return int
     */
    private function getIndexOrZero($releve, $index)
    {
        $value = $releve->valueAtIndex($index);
        if (empty($value)) {
             return 0;
        }
        return $value;
    }
}
