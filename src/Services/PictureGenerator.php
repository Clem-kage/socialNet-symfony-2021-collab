<?php


namespace App\Services;


class PictureGenerator
{
    public function pictureGenerate($username, $save = true)
    {
        $hash = md5($username);
        $color = substr($hash, 0, 6);

        $image = imagecreate(150, 150);

        // couleur random ($hash md5) - couleur de fond
        $red = hexdec(substr($color, 0, 2));
        $green = hexdec(substr($color, 2, 2));
        $blue = hexdec(substr($color, 4, 2));

        imagecolorallocate($image, $red , $green , $blue);

        $colors = [
            $skyBlue = imagecolorallocate($image, 95, 168, 220),
            $black = imagecolorallocate($image, 0, 0, 0),
            $white = imagecolorallocate($image, 255, 255, 255),
            $paleGreen = imagecolorallocate($image, 100, 204, 100),
            $red = imagecolorallocate($image, 255, 50, 50),
            $yellow = imagecolorallocate($image, 255, 255, 50),
            $orange = imagecolorallocate($image, 255, 150, 50),
            $purple = imagecolorallocate($image, 150, 0, 150)
        ];

        $randColor = $colors[rand(0, 7)];

        // Sourcils
        imagefilledrectangle($image, 20, 30, 60, 40, $randColor);
        imagefilledrectangle($image, 150 - 20, 30, 150 - 60, 40, $randColor);

        // "Yeux"
        imagearc($image, 40, 55, 10, 10, 0, 360, $colors[rand(0, 7)]);
        imagearc($image, 150 - 40, 55, 10, 10, 0, 360, $colors[rand(0, 7)]);
        imagefill($image, 40, 55, $colors[rand(0, 7)]);
        imagefill($image, 150 - 40, 55, $colors[rand(0, 7)]);

        // Bouche
        imagearc($image, 75, 75, 150, 100, 25, 155, $randColor);
        imagearc($image, 75, 75, 145, 120, 20, 160, $randColor);
        imagefill($image, 75, 130, $randColor);


        function centrage_texte($z, $y)
        {
            $a = strlen($z);
            $b = imagefontwidth($y);
            $c = $a * $b;
            $d = 150 - $c;
            return $d / 2;
        }

        imagestring($image, 5, centrage_texte($username, 5), 60, $username, $white);
        ImageRectangle($image, 0, 0, 149, 149, $colors[rand(0, 7)]);


        // On enregistre dans uploads/pictures
        if ($save) {
            imagepng($image, "uploads/pictures/$username.png");
        }

        // On display l'image
        imagepng($image);
    }
}