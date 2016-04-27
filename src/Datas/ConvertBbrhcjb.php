<?php

namespace Mactronique\TeleReleve\Datas;

class ConvertBbrhcjb implements ConverterInterface
{
    /**
     * @param string $code
     * @param string $value
     */
    public static function convert($code, $value)
    {
        return doubleval($value);
    }
}
