<?php

namespace mycaptcha;
class MyCaptcha extends Text
{

    public static function get()
    {
        $line_num = rand(3, 7); // рандомное количество линий на капче
        $code = self::generate_text(); // принимаем рандомные символы
        $img_arr = ["1.jpeg", '2.jpg', '3.jpg']; //рандомный фон
        $img = array_rand($img_arr, 1); // рандомный индекс из массива фонов
        $img = ImageCreateFromJPEG(__DIR__ . '/background/' .$img_arr[$img]); //  присваем фон по индексу
        $font =  __DIR__ . '/fonts/arial.ttf';  // путь к шрифту
        $w = imagesx($img) / 2; //центр изображения по вертикали
        $h = imagesy($img) / 2; // центр изображения по горизонтали
        $font_box = imagettfbbox(rand(19, 30) , 0, $font, $code); // рамка текста
        $x = $w - round(($font_box[2] - $font_box[0]) / 2);  //расположение текста на фоне
        $y = $h - round(($font_box[7] - $font_box[1]) / 2);

        for ($i = 0;$i < $line_num;$i++) // случаное количество линий на капче
        {
            $line_color = imagecolorallocate($img, rand(0, 150) , rand(0, 100) , rand(0, 150)); // рандомный цвет c изображения
            imageline($img, rand(0, 20) , rand(1, 50) , rand(150, 180) , rand(1, 50) , $line_color);

        }

        for ($i = 0;$i < strlen($code);$i++) //каждая буква рандомна наклонена
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

