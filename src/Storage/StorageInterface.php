<?php

declare(strict_types=1);

namespace Mactronique\TeleReleve\Storage;

use Mactronique\TeleReleve\Compteur\ReleveInterface;
use Psr\Log\LoggerAwareInterface;

interface StorageInterface extends LoggerAwareInterface
{
    /**
     * Save the releve.
     */
    public function save(ReleveInterface $releve);

    /**
     * Return the array of configuration.
     *
     * @return array
     */
    public function configuration();

    /**
     * @param string $at
     *
     * @return array
     */
    public function read($at);
}
