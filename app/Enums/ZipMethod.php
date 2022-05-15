<?php

namespace App\Enums;

class ZipMethod
{
    use EnumTrait;

    public const PHP = 'php';
    public const LINUX = 'linux';
    public const POWERSHELL = 'powershell';

    public static function variants(): array
    {
        return [
            self::PHP,
            self::LINUX,
            self::POWERSHELL
        ];
    }
}
