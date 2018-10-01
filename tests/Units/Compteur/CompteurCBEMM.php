<?php
/**
 * This file is part of Mactronique EDF TeleReleve package.
 *
 * @author Jean-Baptiste Nahan <jbnahan@gmail.com>
 * @copyright 2016 - Jean-Baptiste Nahan
 * @license MIT
 */

namespace Mactronique\TeleReleve\Tests\Units\Compteur;

use atoum;

class CompteurCBEMM extends atoum
{
    public function testinit()
    {
        $this
            ->assert('init')
                ->given($this->testedInstance = function () {
                    return \Mactronique\TeleReleve\Compteur\CompteurCBEMM::makeFromDevicePath('/dev/tty1');
                })
                ->then
                ->object($this->testedInstance)->isInstanceOf('Mactronique\TeleReleve\Compteur\CompteurCBEMM')
            ->assert('tty not found')
                ->given($this->testedInstance = function () {
                    return \Mactronique\TeleReleve\Compteur\CompteurCBEMM::makeFromDevicePath('/dev/tty1');
                })
                ->and($this->function->file_exists = false)
                ->then
                ->exception(function ($atoum) {
                    $atoum->testedInstance->read();
                })->isInstanceOf('Mactronique\TeleReleve\Compteur\CompteurException')
            ->assert('tty found')
                ->given($this->testedInstance = function () {
                    return \Mactronique\TeleReleve\Compteur\CompteurCBEMM::makeFromDevicePath(dirname(dirname(__DIR__)).'/fixtures/datas.bin');
                })
                ->and($this->function->file_exists = true)
                /*->and($this->function->fopen = function($path, $mode){
                    return fopen(__DIR__.'/../../fixtures/data.bin', 'r');
                })
                ->and($this->function->fread = function($handle, $length) {
                    return fread($handle, $length);
                })
                ->and($this->function->fclose = function($handle) {
                    return fclose($handle);
                })*/
                ->then($releve = $this->testedInstance->read())
                ->object($releve)->isInstanceOf('Mactronique\TeleReleve\Compteur\Releve')

        ;

    }
}