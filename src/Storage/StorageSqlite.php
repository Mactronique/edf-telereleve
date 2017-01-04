<?php

namespace Mactronique\TeleReleve\Storage;

use Mactronique\TeleReleve\Compteur\ReleveInterface;

class StorageSqlite implements StorageInterface
{
    private $path;

    /**
     * @param string $path the path to the SQLITE File.
     */
    public function __construct(array $config)
    {
        $this->path = $config['path'];
    }
    /**
     * Save the releve
     * @param ReleveInterface $releve
     * @return mixed
     */
    public function save(ReleveInterface $releve)
    {
        $db = new \Sqlite3($this->path);

        $db->exec('CREATE TABLE IF NOT EXISTS releve (at TEXT PRIMARY KEY, ptec TEXT, iinst REAL, hchc REAL, hchp REAL, base REAL);');

        if ($db->busyTimeout(5000)) {
            $db->exec(sprintf(
                "INSERT INTO releve (at, ptec, iinst, hchc, hchp, base) VALUES ('%s', '%s', '%s', '%s', '%s', '%s')",
                $releve->at()->format('Y-m-d H:i:s'),
                $releve->valueAtIndex('PTEC'),
                $releve->valueAtIndex('IINST'),
                $releve->valueAtIndex('HCHC'),
                $releve->valueAtIndex('HCHP'),
                $releve->valueAtIndex('BASE')
            ));
        }

    }


    /**
     * Return the array of configuration
     * @return array
     */
    public function configuration()
    {
        return ['path'=>$this->path];
    }

    public function read($at)
    {
        $db = new \Sqlite3($this->path);

        $db->exec('CREATE TABLE IF NOT EXISTS releve (at TEXT, ptec TEXT, iinst REAL, hchc REAL, hchp REAL, base REAL);');

        if ($db->busyTimeout(5000)) {
            $result = $db->query(sprintf(
                "SELECT * FROM releve WHERE at like '%s %%' AND hchc != '' AND hchp != '' ORDER BY at ASC",
                $at
            ));
            $datas = [];
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $datas[] = $row;
            }
            return $datas;
        }
    }
}
