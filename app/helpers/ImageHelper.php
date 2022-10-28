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

        return 'https://avatars.dicebear.com/v2/initials/' . $name . '.svg';
    }
}
