<?php
$inputFile = 'index.png';
$outputFile = 'logo_transparent.png';

if (!file_exists($inputFile)) {
    die("File not found");
}

$img = imagecreatefrompng($inputFile);
if (!$img) {
    die("Failed to create image");
}

$width = imagesx($img);
$height = imagesy($img);

// Create a new true color image with alpha channel
$out = imagecreatetruecolor($width, $height);
imagesavealpha($out, true);
$transparent = imagecolorallocatealpha($out, 0, 0, 0, 127);
imagefill($out, 0, 0, $transparent);

// Sample the top-left pixel to get the background color
$bg_rgb = imagecolorat($img, 1, 1);
$bg_colors = imagecolorsforindex($img, $bg_rgb);
$threshold = 20; // Tolerance

for ($x = 0; $x < $width; $x++) {
    for ($y = 0; $y < $height; $y++) {
        $rgb = imagecolorat($img, $x, $y);
        $colors = imagecolorsforindex($img, $rgb);
        
        $diffR = abs($colors['red'] - $bg_colors['red']);
        $diffG = abs($colors['green'] - $bg_colors['green']);
        $diffB = abs($colors['blue'] - $bg_colors['blue']);
        
        // If color is close to background, make it transparent
        if ($diffR < $threshold && $diffG < $threshold && $diffB < $threshold) {
             // Leave transparent (don't copy pixel)
        } else {
             imagesetpixel($out, $x, $y, imagecolorallocatealpha($out, $colors['red'], $colors['green'], $colors['blue'], $colors['alpha']));
        }
    }
}

imagepng($out, $outputFile);
imagedestroy($img);
imagedestroy($out);
echo "Transparent logo generated: $outputFile\n";
?>
