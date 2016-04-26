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
	public function testinit(){
		$this
			->assert('init')
				->given($this->newTestedInstance())
				->if($this->testedInstance->defineDevicePath('/dev/tty1'))
				->then
				->object($this->testedInstance)->isInstanceOf('Mactronique\TeleReleve\Compteur\CompteurCBEMM')
			->assert('tty not found')
				->given($this->newTestedInstance())
				->if($this->testedInstance->defineDevicePath('/dev/tty1'))
				->and($this->function->file_exists = false)
				->then
				->exception(function ($atoum) {
					$atoum->testedInstance->readDevice();
				})->isInstanceOf('RuntimeException');

		;

	}
}