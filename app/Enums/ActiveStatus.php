<?php

namespace App\Enums;

class ActiveStatus
{
    use EnumTrait;

    public const ACTIVE = 10;
    public const INACTIVE = 0;

    public static function variants(): array
    {
        return [
            self::ACTIVE,
            self::INACTIVE
        ];
    }

    public static function toLabels(): array
    {
        return [
            self::ACTIVE => 'success',
            self::INACTIVE => 'danger'
        ];
    }

    public static function toSelect(): array
    {
        return [
            self::ACTIVE => 'Активно',
            self::INACTIVE => 'Неактивно'
        ];
    }
}
