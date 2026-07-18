<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db_connection.php';
require_once __DIR__ . '/../includes/utils.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$email = trim($_POST['email'] ?? '');

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Valid email address is required.']);
    exit;
}

try {
    $pdo = get_db_connection();
    
    // Check if email already in use
    $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
    $checkStmt->execute(['email' => $email]);
    if ($checkStmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Email is already registered.']);
        exit;
    }
    
    // Generate 6 digit OTP
    $otp = sprintf("%06d", mt_rand(1, 999999));
    
    // Store in session (with expiration 15 mins)
    $_SESSION['registration_otp'] = [
        'code' => $otp,
        'email' => $email,
        'expires_at' => time() + (15 * 60)
    ];
    
    // Send email
    $subject = "Your Registration Verification Code";
    $body = "<p>Hello,</p>
             <p>Thank you for starting your registration with Sovryx Tech.</p>
             <p>Your email verification code is: <strong>{$otp}</strong></p>
             <p>This code will expire in 15 minutes.</p>
             <p>If you did not request this, please ignore this email.</p>";
             
    if (send_notification_email($email, $subject, $body)) {
        echo json_encode(['success' => true, 'message' => 'Verification code sent successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send verification code. Please try again.']);
    }
} catch (Exception $e) {
    error_log("OTP Send Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while generating OTP.']);
}
