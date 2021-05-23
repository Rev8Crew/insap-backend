<?php


namespace App\helpers;

/**
 * Class ImageHelper
 * @package App\helpers
 */
class ImageHelper {
    /**
     * @param $name
     * @return string
     */
    public static function getAvatarImage($name ): string
    {
        return 'https://avatars.dicebear.com/v2/initials/' . preg_replace('/[^a-z0-9 _.-]+/i', '', $name) . '.svg';
    }
}
