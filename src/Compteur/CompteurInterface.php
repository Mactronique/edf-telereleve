<?php

namespace Mactronique\TeleReleve\Compteur;

interface CompteurInterface
{
    /*
     * @return ReleveInterface
     */
    public function read();
}
