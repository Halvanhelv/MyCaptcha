<?php


namespace mycaptcha;


class Text
{

    public static function generate_text()
    {
        define('CHARS','ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789');
        define('LENGTH','4');
        return substr(str_shuffle(CHARS), 0, LENGTH);


    }
}
