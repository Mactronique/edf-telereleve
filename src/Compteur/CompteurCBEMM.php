<?php
/**
 * This file is part of Mactronique EDF TeleReleve package.
 *
 * @author Jean-Baptiste Nahan <jbnahan@gmail.com>
 * @copyright 2016 - Jean-Baptiste Nahan
 * @license MIT
 */

namespace Mactronique\TeleReleve\Compteur;

class CompteurCBEMM implements CompteurInterface
{
    private $dev;

    /**
     * @param string $device
     */
    public function defineDevicePath($device)
    {
        $this->dev = $device;
    }

    /*
     * @return ReleveInterface
     */
    public function read()
    {

    }

    private function readDevice(){
        if(!file_exists($this->dev)){
            throw new \RuntimeException("The device does not exist : ".$this->dev, 1);
        }

        $handle = fopen ($this->dev, "r"); // ouverture du flux

        while (fread($handle, 1) != chr(2)); // on attend la fin d'une trame pour commencer a avec la trame suivante

        $char  = '';
        $trame = '';
        $datas = '';

        while ($char != chr(2)) { // on lit tous les caracteres jusqu'a la fin de la trame
          $char = fread($handle, 1);
          if ($char != chr(2)){
            $trame .= $char;
          }
        }

        fclose ($handle); // on ferme le flux

        $trame = chop(substr($trame,1,-1)); // on supprime les caracteres de debut et fin de trame

        $messages = explode(chr(10), $trame); // on separe les messages de la trame

    }
}