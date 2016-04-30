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
    public static function makeFromDevicePath($device)
    {
        $compteur = new self();
        $compteur->dev = $device;
        return $compteur;
    }

    /*
     * @return ReleveInterface
     */
    public function read()
    {
        $datas = $this->readDevice();
        return Releve::makeFromData('CBEMM', $datas);
    }

    protected function readDevice()
    {
        $trame = '';
        $i=0;
        while (strlen($trame)==0 && $i < 3) {
            $trame = $this->readTrame();
            $i++;
        }

        $trame = chop(substr($trame, 1, -1)); // on supprime les caracteres de debut et fin de trame

        $messages = explode(chr(10), $trame); // on separe les messages de la trame
        $new = [];
        foreach ($messages as $msg) {
            $ligne = explode(' ', $msg, 3);
            if (count($ligne)<2) {
                continue;
            }
            $new[$ligne[0]] = $ligne[1];
        }
        return $new;
    }

    protected function readTrame()
    {
        if (!file_exists($this->dev)) {
            throw new \RuntimeException("The device does not exist : ".$this->dev, 1);
        }

        $handle = fopen($this->dev, "r"); // ouverture du flux
        if (false === $handle) {
            throw new \RuntimeException("The device does not ready for read : ".$this->dev, 1);
        }

        while(fread($handle, 1) != chr(2)); // on attend la fin d'une trame pour commencer a avec la trame suivante

        $char  = '';
        $trame = '';
        $datas = '';

        while ($char != chr(2)) { // on lit tous les caracteres jusqu'a la fin de la trame
            $char = fread($handle, 1);
            if ($char != chr(2)) {
                $trame .= $char;
            }
        }

        fclose($handle); // on ferme le flux
        return $trame;
    }
}
