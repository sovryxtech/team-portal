<?php
declare(strict_types=1);

/**
 * API Handler for Contact Form submissions
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../includes/db_connection.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/utils.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed.']);
    exit;
}

if (!csrf_verify()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'CSRF verification failed. Request blocked.']);
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if (empty($name) || empty($email) || empty($subject) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'All form fields are required.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

try {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (:name, :email, :subject, :message)");
    $stmt->execute([
        'name'    => $name,
        'email'   => $email,
        'subject' => $subject,
        'message' => $message
    ]);

    // Send email alert to Admin/HR
    $emailSubject = "New Contact Support Message: " . $subject;
    $emailBody = "<h3>A new contact support message has been submitted.</h3>
                  <p><strong>Name:</strong> " . htmlspecialchars($name) . "</p>
                  <p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>
                  <p><strong>Subject:</strong> " . htmlspecialchars($subject) . "</p>
                  <p><strong>Message:</strong></p>
                  <p>" . nl2br(htmlspecialchars($message)) . "</p>";
    
    $adminEmailsStmt = $pdo->query("SELECT email FROM users WHERE role_id = 1 OR role_id = 2");
    while ($adminEmail = $adminEmailsStmt->fetchColumn()) {
        send_notification_email($adminEmail, $emailSubject, $emailBody);
    }

    // Log guest activity
    log_activity(null, 'Contact form submitted', "Sender: {$name}, Subject: {$subject}");

    echo json_encode(['success' => true, 'message' => 'Your message has been submitted successfully. HR support will review it shortly.']);
} catch (\Exception $e) {
    error_log("Contact submit error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'A system error occurred. Please try again.']);
}
