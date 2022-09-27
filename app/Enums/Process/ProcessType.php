<?php

namespace App\Enums\Process;

use App\Enums\EnumTrait;

class ProcessType
{
    use EnumTrait;

    public const IMPORTER = 1;
    public const EXPORTER = 2;

    public static function variants(): array
    {
        return [
            self::IMPORTER,
            self::EXPORTER
        ];
    }

    public static function labels(): array
    {
        return [
            self::IMPORTER => 'importer',
            self::EXPORTER => 'exporter',
        ];
    }
}
