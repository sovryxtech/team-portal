<?php
declare(strict_types=1);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$otpInput = trim($_POST['otp'] ?? '');
$emailInput = trim($_POST['email'] ?? '');

if (empty($otpInput) || empty($emailInput)) {
    echo json_encode(['success' => false, 'message' => 'OTP and email are required.']);
    exit;
}

if (!isset($_SESSION['registration_otp'])) {
    echo json_encode(['success' => false, 'message' => 'No OTP session found. Please request a new code.']);
    exit;
}

$sessionOtp = $_SESSION['registration_otp'];

if ($sessionOtp['email'] !== $emailInput) {
    echo json_encode(['success' => false, 'message' => 'Email address mismatch.']);
    exit;
}

if (time() > $sessionOtp['expires_at']) {
    unset($_SESSION['registration_otp']);
    echo json_encode(['success' => false, 'message' => 'OTP has expired. Please request a new code.']);
    exit;
}

if ($sessionOtp['code'] === $otpInput) {
    // Verified!
    $_SESSION['email_verified'] = $emailInput;
    unset($_SESSION['registration_otp']);
    echo json_encode(['success' => true, 'message' => 'Email verified successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid OTP code.']);
}
