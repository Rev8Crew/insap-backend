<?php

namespace App\Enums;

class ResponseStatus
{
    use EnumTrait;

    public const SUCCESS    = 0;
    public const ERROR = 1;
    public const UNKNOWN = 2;

    public static function variants(): array
    {
        return [
            self::SUCCESS,
            self::ERROR,
            self::UNKNOWN
        ];
    }
}
