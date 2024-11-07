<?php

declare(strict_types=1);

namespace Mactronique\TeleReleve\Datas;

class ConvertBbrhcjb implements ConverterInterface
{
    public static function convert(string $code, string $value)
    {
        return (float) $value;
    }
}
