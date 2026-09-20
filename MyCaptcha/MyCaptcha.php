<?php

namespace mycaptcha;

/**
 * Generates a CAPTCHA image and verifies user answers via PHP sessions.
 *
 * No image files are written to disk: the generated JPEG is returned as a
 * base64 data URI, and the expected code is kept only in $_SESSION.
 */
class MyCaptcha
{
    private const DEFAULT_TTL = 300; // seconds

    private const SESSION_KEY = 'mycaptcha';

    private const BACKGROUNDS = ['1.jpeg', '2.jpg', '3.jpg'];

    private const DEFAULT_FONT = 'Roboto-Black.ttf';

    /**
     * Generate a new CAPTCHA and store its expected answer in the session.
     *
     * @param int         $length Number of characters in the code.
     * @param int         $ttl    Seconds before the code expires.
     * @param string|null $font   Font file name inside fonts/, defaults to a bundled open-licensed font.
     *
     * @return string A `data:image/jpeg;base64,...` URI ready to use as an <img src="">.
     */
    public static function generate(int $length = 4, int $ttl = self::DEFAULT_TTL, ?string $font = null): string
    {
        self::ensureSession();

        $code = Text::generate($length);
        $img = self::renderImage($code, $font ?? self::DEFAULT_FONT);

        ob_start();
        imagejpeg($img);
        $binary = ob_get_clean();
        imagedestroy($img);

        $_SESSION[self::SESSION_KEY] = [
            'code' => $code,
            'expires' => time() + $ttl,
        ];

        return 'data:image/jpeg;base64,' . base64_encode($binary);
    }

    /**
     * Verify a user's answer against the code stored in the session.
     *
     * The stored code is consumed (removed from the session) whether or not
     * it matches, so a given CAPTCHA can only ever be checked once.
     */
    public static function verify(string $answer, bool $caseSensitive = false): bool
    {
        self::ensureSession();

        $stored = $_SESSION[self::SESSION_KEY] ?? null;
        unset($_SESSION[self::SESSION_KEY]);

        if ($stored === null) {
            return false;
        }

        if (time() > $stored['expires']) {
            return false;
        }

        $code = $stored['code'];

        if (!$caseSensitive) {
            $code = strtolower($code);
            $answer = strtolower($answer);
        }

        return hash_equals($code, $answer);
    }

    private static function renderImage(string $code, string $font)
    {
        $backgroundFile = self::BACKGROUNDS[array_rand(self::BACKGROUNDS)];
        $backgroundPath = __DIR__ . '/background/' . $backgroundFile;
        $fontPath = __DIR__ . '/fonts/' . $font;

        if (!is_readable($backgroundPath)) {
            throw new \RuntimeException("Background image not found: {$backgroundPath}");
        }
        if (!is_readable($fontPath)) {
            throw new \RuntimeException("Font not found: {$fontPath}");
        }

        $img = imagecreatefromjpeg($backgroundPath);

        $lineCount = random_int(3, 7);
        for ($i = 0; $i < $lineCount; $i++) {
            $color = imagecolorallocate($img, random_int(0, 150), random_int(0, 100), random_int(0, 150));
            imageline($img, random_int(0, 20), random_int(1, 50), random_int(150, 180), random_int(1, 50), $color);
        }

        $w = imagesx($img) / 2;
        $h = imagesy($img) / 2;
        $box = imagettfbbox(24, 0, $fontPath, $code);
        $x = $w - round(($box[2] - $box[0]) / 2);
        $y = $h - round(($box[7] - $box[1]) / 2);

        for ($i = 0, $len = strlen($code); $i < $len; $i++) {
            $color = imagecolorallocate($img, random_int(0, 150), random_int(0, 100), random_int(0, 150));
            $letter = $code[$i];
            imagettftext($img, random_int(19, 30), random_int(-8, 8), $x, $y, $color, $fontPath, $letter);
            $x += random_int(20, 26);
        }

        return $img;
    }

    private static function ensureSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}
