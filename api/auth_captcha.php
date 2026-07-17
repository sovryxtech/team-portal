<?php
declare(strict_types=1);

/**
 * Native CAPTCHA Image Generator utilizing GD Library
 */

require_once __DIR__ . '/../includes/auth.php';

// Generate random string
$characters = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjklmnpqrstuvwxyz23456789';
$phrase = '';
$length = 5;
for ($i = 0; $i < $length; $i++) {
    $phrase .= $characters[random_int(0, strlen($characters) - 1)];
}

// Store in session
$_SESSION['captcha_phrase'] = $phrase;

// Create image
$width = 130;
$height = 42;
$image = imagecreatetruecolor($width, $height);

// Color palette
$bg = imagecolorallocate($image, 244, 246, 249); // light grey matching background
$primary = imagecolorallocate($image, 11, 37, 69); // theme primary deep blue
$noiseColor = imagecolorallocate($image, 238, 150, 75); // theme secondary orange

// Fill background
imagefilledrectangle($image, 0, 0, $width, $height, $bg);

// Add background noise lines
for ($i = 0; $i < 4; $i++) {
    imageline($image, random_int(0, $width), random_int(0, $height), random_int(0, $width), random_int(0, $height), $noiseColor);
}

// Add noise dots
for ($i = 0; $i < 80; $i++) {
    imagesetpixel($image, random_int(0, $width), random_int(0, $height), $noiseColor);
}

// Add characters using built-in font
for ($i = 0; $i < $length; $i++) {
    $char = $phrase[$i];
    $x = 15 + ($i * 20) + random_int(-3, 3);
    $y = 12 + random_int(-4, 4);
    // Draw built-in large font character
    imagechar($image, 5, $x, $y, $char, $primary);
}

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Content-Type: image/png");

// Output PNG
imagepng($image);
imagedestroy($image);
