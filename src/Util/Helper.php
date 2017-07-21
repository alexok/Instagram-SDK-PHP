<?php
/**
 * Created by PhpStorm.
 * User: alexok
 * Date: 21.07.17
 * Time: 16:55
 */

namespace Instagram\Util;


class Helper
{
    public static function generateUploadId()
    {
        return number_format(round(microtime(true) * 1000), 0, '', '');
    }
}