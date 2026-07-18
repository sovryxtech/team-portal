<?php
ob_start();
include 'api/auth_captcha.php';
$output = ob_get_clean();

$hex = bin2hex(substr($output, 0, 10));
echo "First 10 bytes: " . $hex . "\n";
if (strpos($output, "\x89PNG") === 0) {
    echo "PNG Signature OK!\n";
} else {
    echo "PNG Signature FAILED!\n";
    echo "Actual string start: " . addcslashes(substr($output, 0, 10), "\0..\37!\177..\377") . "\n";
}
