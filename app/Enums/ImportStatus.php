<?php

namespace App\Enums;

class ImportStatus
{
    use EnumTrait;

    public const INITIAL = 0;
    public const PROCESSING = 1;
    public const SUCCESS = 2;
    public const ERROR = 3;

    public static function variants(): array
    {
        return [
            self::INITIAL,
            self::PROCESSING,
            self::SUCCESS,
            self::ERROR
        ];
    }
}
