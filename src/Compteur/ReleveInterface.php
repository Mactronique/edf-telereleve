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

interface ReleveInterface
{
    /**
     * @return \DateTimeImmutable
     */
    public function at();

    /**
     * @return array
     */
    public function index();

    /**
     * @param string $code
     */
    public function valueAtIndex($code);
}
