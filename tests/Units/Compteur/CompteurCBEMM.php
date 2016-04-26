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
		$this->assert('init')
			->if($this->newTestedInstance())
			->and($this->testedInstance->defineDevicePath('/dev/tty1'))
			->then()
				->object($this->testedInstance)->isInstanceOf('Mactronique\TeleReleve\Compteur\CompteurCBEMM')
		;

	}
}