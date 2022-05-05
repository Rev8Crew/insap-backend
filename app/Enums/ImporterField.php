<?php

namespace App\Enums;

class ImporterField
{
    use EnumTrait;

    public const FIELD_NUMBER = 1;
    public const FIELD_STRING = 2;
    public const FIELD_FILE = 3;
    public const FIELD_DATE = 4;
    public const FIELD_TIME = 5;
    public const FIELD_DATETIME = 6;


    public static function variants(): array
    {
        return [
            self::FIELD_NUMBER,
            self::FIELD_STRING,
            self::FIELD_FILE,
            self::FIELD_DATE,
            self::FIELD_TIME,
            self::FIELD_DATETIME
        ];
    }
}
