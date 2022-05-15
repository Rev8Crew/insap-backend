<?php

namespace App\Enums;

class ImportStatus
{
    use EnumTrait;

    public const INITIAL = 0;
    public const SUCCESS = 1;
    public const ERROR = 2;

    public static function variants(): array
    {
        return [
            self::INITIAL,
            self::SUCCESS,
            self::ERROR
        ];
    }
}
