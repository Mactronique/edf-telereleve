<?php

declare(strict_types=1);

namespace Mactronique\TeleReleve\Datas;

class ConvertAdps implements ConverterInterface
{
    public static function convert(string $code, string $value)
    {
        return (int) $value;
    }
}
