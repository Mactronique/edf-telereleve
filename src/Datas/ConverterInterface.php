<?php

namespace Mactronique\TeleReleve\Datas;

interface ConverterInterface
{
    /**
     * @param string $code
     * @param string $value
     */
    public static function convert($code, $value);
}
