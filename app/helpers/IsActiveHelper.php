<?php

namespace App\helpers;

class IsActiveHelper
{
    const ACTIVE_ACTIVE = 1;
    const ACTIVE_INACTIVE = 0;

    /**
     * @param $model
     *
     * @return int
     */
    public static function isActive( $model ): int
    {
        if (isset($model->is_active)) {
            return !$model->is_active ? self::ACTIVE_INACTIVE : self::ACTIVE_ACTIVE;
        }

        return self::ACTIVE_ACTIVE;
    }

    public static function validate( int $value ): bool
    {
        return in_array($value, [ self::ACTIVE_ACTIVE, self::ACTIVE_INACTIVE]);
    }
}
