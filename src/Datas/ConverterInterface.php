<?php

declare(strict_types=1);

namespace Mactronique\TeleReleve\Datas;

interface ConverterInterface
{
    public static function convert(string $code, string $value);
}
