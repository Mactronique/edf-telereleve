<?php
/**
 * This file is part of Mactronique EDF TeleReleve package.
 *
 * @author Jean-Baptiste Nahan <jbnahan@gmail.com>
 * @copyright 2016 - Jean-Baptiste Nahan
 * @license MIT
 */

namespace Mactronique\TeleReleve\Compteur;

class CompteurCBETM extends CompteurCBEMM implements CompteurInterface
{
    

    /**
     * @return ReleveInterface
     *
     * @throws CompteurException
     */
    public function read()
    {
        $datas = $this->readDevice();
        return Releve::makeFromData('CBETM', $datas);
    }

}
