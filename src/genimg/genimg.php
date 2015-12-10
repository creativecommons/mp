<?php
header('Content-Type: image/png');

////////////////////////////////////////////////////////////////////////////////
// Configuration
////////////////////////////////////////////////////////////////////////////////

// Replace path by your own font path
$font = './cc-icons.ttf';

// Don't let people render text of arbitrary length
$max_text_length = 3;

// Make sure we only render valid license strings, in the right order
$licenses = ['0' => '0',
             'b' => 'b',
             'ab' => 'ba',
             'abn' => 'ban',
             'bn' => 'bn',
             'bd' => 'bd',
             'bdn' => 'bnd'
];

////////////////////////////////////////////////////////////////////////////////
// Setup
////////////////////////////////////////////////////////////////////////////////

// Get height and width
//FIXME: if only one or the other is set, calculate size proportionally
$height = 31;
$width = 88;
if (isset($_REQUEST['h'])) {
    $height = intval($_REQUEST['h']);
}
if (isset($_REQUEST['w'])) {
    $width = intval($_REQUEST['w']);
}

// Get the text to draw, limited to the maximum length and carefully sorted
$text = substr($_REQUEST["l"], 0, $max_text_length);
$text = str_split($text);
sort($text);
$text = implode($text);
// If the text is bad, crash out rather than render it
if ($licenses[$text] == null) {
    http_response_code(422);
    exit;
}
$text = $licenses[$text];

// Create the image
// We have to do this first so we can allocate the colors using it
$im = imagecreatetruecolor($width, $height);
imagesavealpha($im, true);

// Set up the colors
// Not that if foreground is transparent and background isn't 100% transparent
// they'll blend
$background_color_spec = 'ffffffff';
$foreground_color_spec = '00000000';
if (isset($_REQUEST['b'])) {
    $background_color_spec = ltrim($_REQUEST['b'], '#');
}
if (isset($_REQUEST['f'])) {
    $foreground_color_spec = ltrim($_REQUEST['f'], '#');
}
$background_color = allocate_color($im, $background_color_spec);
$foreground_color = allocate_color($im, $foreground_color_spec);

// Calculate the font size
$size = floor($height / 1.29); // 24;
$lineheight = $size + ($size / 6);

// Calculate the padding
//FIXME: Calculate initial offset as well
switch (strlen($text)) {

case 1:
    $padding = (floor($width - $size) / 2) - 1; //29;
    break;

case 2:
    $padding = floor($width - $size) / 4; //15;
    break;

case 3:
    $padding = 2; //1;
    break;
}

////////////////////////////////////////////////////////////////////////////////
// Draw the image (main flow of execution)
////////////////////////////////////////////////////////////////////////////////

imagefill($im, 0, 0, $background_color);

// Add some shadow to the text
//imagettftext($im, 20, 0, 11, 21, $grey, $font, $text);

imagettftextSp($im, $size, 0, $padding, $lineheight, $foreground_color, $font,
               $text, 1);

// Using imagepng() results in clearer text compared with imagejpeg()
imagepng($im);
imagedestroy($im);

////////////////////////////////////////////////////////////////////////////////
// Functions
////////////////////////////////////////////////////////////////////////////////

// Only accept rgb/rgba 6 or 8 digit hex color descriptions
function allocate_color ($img, $spec) {
    $elements = str_split($spec, 2);
    $num_elements = count($elements);
    if ($num_elements < 3 || $num_elements > 4) {
        http_response_code(422);
        exit;
    }
    // Assume opaque
    if ($num_elements == 3) {
        $elements[] = '00';
    }
    return imagecolorallocatealpha($img,
                                   hexdec($elements[0]),
                                   hexdec($elements[1]),
                                   hexdec($elements[2]),
                                   // Alpha is 0..127
                                   hexdec($elements[3]) / 2);
}

function imagettftextSp($image, $size, $angle, $x, $y, $color, $font, $text,
                        $spacing = 0) {
    if ($spacing == 0) {
        imagettftext($image, $size, $angle, $x, $y, $color, $font, $text);
    } else {
        $temp_x = $x;
        $temp_y = $y;
        for ($i = 0; $i < strlen($text); $i++) {
            imagettftext($image, $size, $angle, $temp_x, $temp_y, $color,
                         $font, $text[$i]);
            $bbox = imagettfbbox($size, 0, $font, $text[$i]);
            $temp_x += cos(deg2rad($angle))
                * ($spacing + ($bbox[2] - $bbox[0]));
            $temp_y -= sin(deg2rad($angle))
                * ($spacing + ($bbox[2] - $bbox[0]));
        }
    }
}

?>
