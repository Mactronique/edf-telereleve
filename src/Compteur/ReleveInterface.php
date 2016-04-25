<?php

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
