<?php
/**
 * This file is part of Mactronique EDF TeleReleve package.
 *
 * @author Jean-Baptiste Nahan <jbnahan@gmail.com>
 * @copyright 2016 - Jean-Baptiste Nahan
 * @license MIT
 */
namespace Mactronique\TeleReleve\Compteur;

interface ReleveInterface
{
    /*
     * @return \DateTimeImmutable
     */
    public function at();

    /*
     * @return array
     */
    public function index();
}
