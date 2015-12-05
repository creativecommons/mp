<?php
header('Content-Type: image/png');

$height = 31;
$width = 88;

// Create the image
$im = imagecreatetruecolor($width, $height);

// Create some colors
$white = imagecolorallocate($im, 255, 255, 255);
$grey = imagecolorallocate($im, 128, 128, 128);
$hotpink = imagecolorallocate($im, 255, 105, 180);
$black = imagecolorallocate($im, 0, 0, 0);
imagefilledrectangle($im, 0, 0, 88, 31, $white);

// The text to draw
$text = '0';
// Replace path by your own font path
$font = './cc-icons.ttf';

// Add some shadow to the text
//imagettftext($im, 20, 0, 11, 21, $grey, $font, $text);

$size = 24;
$lineheight = $size + ($size / 6);

// 15 for 2 icons,
// 1 for 3 icons

$n = strlen($text);

switch ($n) {

case 1:
    $padding = 29;
    break;

case 2:
    $padding = 15;
    break;

case 3:

    $padding = 1;
    break;
}

imagettftextSp($im, $size, 0, $padding, $lineheight, $grey, $font, $text, 1);

// Using imagepng() results in clearer text compared with imagejpeg()
imagepng($im);
imagedestroy($im);

function imagettftextSp($image, $size, $angle, $x, $y, $color, $font, $text, $spacing = 0)
{        
    if ($spacing == 0)
    {
        imagettftext($image, $size, $angle, $x, $y, $color, $font, $text);
    }
    else
    {
        $temp_x = $x;
        $temp_y = $y;
        for ($i = 0; $i < strlen($text); $i++)
        {
            imagettftext($image, $size, $angle, $temp_x, $temp_y, $color, $font, $text[$i]);
            $bbox = imagettfbbox($size, 0, $font, $text[$i]);
            $temp_x += cos(deg2rad($angle)) * ($spacing + ($bbox[2] - $bbox[0]));
            $temp_y -= sin(deg2rad($angle)) * ($spacing + ($bbox[2] - $bbox[0]));
        }
    }
}

?>