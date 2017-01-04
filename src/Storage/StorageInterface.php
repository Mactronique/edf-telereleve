<?php

namespace Mactronique\TeleReleve\Storage;

use Mactronique\TeleReleve\Compteur\ReleveInterface;

interface StorageInterface
{
    /**
     * Save the releve
     * @param ReleveInterface $releve
     * @return mixed
     */
    public function save(ReleveInterface $releve);

    /**
     * Return the array of configuration
     * @return array
     */
    public function configuration();
}
