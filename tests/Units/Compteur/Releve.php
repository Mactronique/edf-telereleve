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

class Releve extends atoum
{
    public function testinit()
    {
        $this
            ->assert('init')
                ->given($this->testedInstance = function () {
                    return \Mactronique\TeleReleve\Compteur\Releve::makeFromData('CBEMM', []);
                })
                ->then
                ->object($this->testedInstance)->isInstanceOf('Mactronique\TeleReleve\Compteur\Releve')
                ->array($this->testedInstance->index())->isEmpty()
                ->object($this->testedInstance->at())->isInstanceOf('DateTimeImmutable')
            ->assert('describe')
                ->given($this->testedInstance = function () {
                    return \Mactronique\TeleReleve\Compteur\Releve::makeFromData('CBEMM', ['ADCO'=>'test', 'OPTARIF'=>'HC..', 'ISOUSC'=>'60', 'HCHC'=>'001498178', 'HCHP'=>'007400125', 'IINST' => '002', 'IMAX' => '043', 'PAPP' => '00500']);
                })
                ->then
                ->array($result = $this->testedInstance->describe())->hasSize(8)
        ;

    }
}
