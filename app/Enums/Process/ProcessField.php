<?php

namespace App\Enums\Process;

use App\Enums\EnumTrait;

class ProcessField
{
    use EnumTrait;

    public const FIELD_NUMBER = "number";
    public const FIELD_DATETIME = "dateTime";
    public const FIELD_STRING = "text";
    public const FIELD_FILE = "file";
    public const FIELD_CHECKBOX = "checkbox";

    //public const FIELD_DATE = 4;
    //public const FIELD_TIME = 5;

    public static function variants(): array
    {
        return [
            self::FIELD_NUMBER,
            self::FIELD_STRING,
            self::FIELD_FILE,
            self::FIELD_DATETIME,
            self::FIELD_CHECKBOX
        ];
    }
}
