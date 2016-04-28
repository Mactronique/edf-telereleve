<?php

namespace Mactronique\TeleReleve\Storage;

use Mactronique\TeleReleve\Compteur\ReleveInterface;

class StorageSqlite implements StorageInterface
{
    private $path;

    /**
     * @param string $path the path to the SQLITE File.
     */
    public function __construct($path)
    {
        $this->path = $path;
    }
    /**
     * Save the releve
     * @param ReleveInterface $releve
     * @return mixed
     */
    public function save(ReleveInterface $releve)
    {
        $db = new \Sqlite3($this->path);

        $db->exec('CREATE TABLE IF NOT EXISTS releve (at TEXT, ptec TEXT, iinst REAL, hchc REAL, hchp REAL, base REAL);');

        if ($db->busyTimeout(5000)) {
            $db->exec(sprintf(
                "INSERT INTO releve (at, ptec, iisnt, hchc, hchp, base) VALUES ('%s', '%s', '%s', '%s', '%s', '%s')",
                $releve->at()->format('Y-m-d H:i:s'),
                $releve->valueAtIndex('PTEC'),
                $releve->valueAtIndex('IINST'),
                $releve->valueAtIndex('HCHC'),
                $releve->valueAtIndex('HCHP'),
                $releve->valueAtIndex('BASE')
            ));
        }

    }
}
