<?php

declare(strict_types=1);
/**
 * This file is part of Mactronique EDF TeleReleve package.
 *
 * @author Jean-Baptiste Nahan <814683+macintoshplus@users.noreply.github.com>
 * @copyright 2016,2024 - Jean-Baptiste Nahan
 * @license MIT
 */

namespace Mactronique\TeleReleve\Compteur;

class CompteurCBETM extends CompteurCBEMM implements CompteurInterface
{
    /**
     * @throws CompteurException
     * @throws \Exception
     */
    public function read(): ReleveInterface
    {
        $datas = $this->readDevice();

        return Releve::makeFromData('CBETM', $datas);
    }
}
