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

class CompteurCBEMM implements CompteurInterface
{
    private ?string $dev = null;

    public static function makeFromDevicePath(string $device): CompteurInterface
    {
        $compteur = new self();
        $compteur->dev = $device;

        return $compteur;
    }

    /**
     * @throws CompteurException
     */
    public function read(): ReleveInterface
    {
        $datas = $this->readDevice();

        return Releve::makeFromData('CBEMM', $datas);
    }

    /**
     * Read the trame from device. Attempt 3 time read.
     *
     * @throws CompteurException
     */
    protected function readDevice(): array
    {
        $trame = '';
        $i = 0;
        while (\strlen($trame) == 0 && $i < 3) {
            $trame = $this->readTrame();
            ++$i;
        }

        echo date('Y-m-d H:i:s T').' Read attempt : '.$i.' for '.\strlen($trame)." octet(s)\n";

        $trame = rtrim(substr($trame, 1, -1)); // on supprime les caracteres de debut et fin de trame

        $messages = explode(\chr(10), $trame); // on separe les messages de la trame
        $new = [];
        foreach ($messages as $msg) {
            $ligne = explode(' ', $msg, 3);
            if (\count($ligne) < 2) {
                continue;
            }
            $new[$ligne[0]] = $ligne[1];
        }

        return $new;
    }

    /**
     * Read the devices for get one trame.
     *
     * @throws CompteurException
     */
    protected function readTrame(): string
    {
        if ($this->dev === null || !file_exists($this->dev)) {
            throw new CompteurException('The device does not exist : '.$this->dev, 1);
        }

        $handle = fopen($this->dev, 'r'); // ouverture du flux
        if ($handle === false) {
            throw new CompteurException('The device does not ready for read : '.$this->dev, 1);
        }

        while (fread($handle, 1) != \chr(2)) {
            // on attend la fin d'une trame pour commencer avec la trame suivante
        }

        $char  = '';
        $trame = '';

        while ($char != \chr(2)) { // on lit tous les caracteres jusqu'à la fin de la trame
            $char = fread($handle, 1);
            if ($char != \chr(2)) {
                $trame .= $char;
            }
        }

        fclose($handle); // on ferme le flux

        return $trame;
    }
}
