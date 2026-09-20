<?php

namespace mycaptcha;

class Text
{
    // 0/O/1/l/I are left out on purpose to avoid ambiguous codes.
    private const CHARS = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789';

    /**
     * Generate a random alphanumeric code using a cryptographically secure RNG.
     */
    public static function generate(int $length = 4): string
    {
        $max = strlen(self::CHARS) - 1;
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= self::CHARS[random_int(0, $max)];
        }

        return $code;
    }
}
