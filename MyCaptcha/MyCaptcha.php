<?php

namespace mycaptcha;
class MyCaptcha extends Text
{

    public static function get()
    {
        $line_num = rand(3, 7); // random number of lines on captcha
        $code = self::generate_text(); // generate random characters
        $img_arr = ["1.jpeg", '2.jpg', '3.jpg']; // random background
        $img = array_rand($img_arr, 1); // random index from backgrounds array
        $img = ImageCreateFromJPEG(__DIR__ . '/background/' .$img_arr[$img]); // assign background by index
        $font =  __DIR__ . '/fonts/arial.ttf';  // path to font
        $w = imagesx($img) / 2; // image center vertically
        $h = imagesy($img) / 2; // image center horizontally
        $font_box = imagettfbbox(rand(19, 30) , 0, $font, $code); // text bounding box
        $x = $w - round(($font_box[2] - $font_box[0]) / 2);  // text position on background
        $y = $h - round(($font_box[7] - $font_box[1]) / 2);

        for ($i = 0;$i < $line_num;$i++) // random number of lines on captcha
        {
            $line_color = imagecolorallocate($img, rand(0, 150) , rand(0, 100) , rand(0, 150)); // random color
            imageline($img, rand(0, 20) , rand(1, 50) , rand(150, 180) , rand(1, 50) , $line_color);

        }

        for ($i = 0;$i < strlen($code);$i++) // each letter is randomly tilted
        {
            $text_color = imagecolorallocate($img, rand(0, 150) , rand(0, 100) , rand(0, 150));
            $letter = substr($code, $i, 1);
            imagettftext($img, rand(19, 40) , rand(2, 10) , $x, $y, $text_color, $font, $letter);
            $x += rand(20, 26);
        }

        imagejpeg($img, __DIR__ . '/captcha/' . $code . '.jpeg');
        imagedestroy($img);
        return $code;
    }
}

