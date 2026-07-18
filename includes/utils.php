<?php
declare(strict_types=1);

/**
 * Common System Utilities & Helpers
 */

require_once __DIR__ . '/db_connection.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * XSS escaping helper
 */
function e(string|null $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Log user activity into the database
 */
function log_activity(int|null $userId, string $action, string|null $details = null): bool {
    try {
        $pdo = get_db_connection();
        $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, details) VALUES (:user_id, :action, :details)");
        return $stmt->execute([
            'user_id' => $userId,
            'action'  => $action,
            'details' => $details
        ]);
    } catch (Exception $e) {
        // Fallback silently in production or write to logs
        error_log("Failed to log activity: " . $e->getMessage());
        return false;
    }
}

/**
 * Log login events into the database
 */
function log_login(int $userId): bool {
    try {
        $pdo = get_db_connection();
        $stmt = $pdo->prepare("INSERT INTO login_logs (user_id, ip_address, user_agent) VALUES (:user_id, :ip_address, :user_agent)");
        return $stmt->execute([
            'user_id'    => $userId,
            'ip_address' => get_client_ip(),
            'user_agent' => get_client_user_agent()
        ]);
    } catch (Exception $e) {
        error_log("Failed to log login: " . $e->getMessage());
        return false;
    }
}

/**
 * Get Client IP Address
 */
function get_client_ip(): string {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    }
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

/**
 * Get Client User Agent
 */
function get_client_user_agent(): string {
    return $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown User Agent';
}

/**
 * Send email using PHPMailer/SMTP with debug logging support
 */
function send_notification_email(string $toEmail, string $subject, string $bodyHTML, string|null $bodyText = null): bool {
    $mailConfig = require __DIR__ . '/../config/mail.php';
    
    // Log email details if debug mode is active
    if ($mailConfig['debug_mode']) {
        $logDir = dirname($mailConfig['debug_log']);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        $logMessage = sprintf(
            "[%s] To: %s | Subject: %s\nBody:\n%s\n---------------------------------------\n",
            date('Y-m-d H:i:s'),
            $toEmail,
            $subject,
            $bodyText ?? strip_tags($bodyHTML)
        );
        file_put_contents($mailConfig['debug_log'], $logMessage, FILE_APPEND);
    }
    
    // If SMTP user is not set, we run in mock mode and just log.
    if (empty($mailConfig['smtp_user'])) {
        return true; 
    }
    
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = $mailConfig['smtp_host'];
        $mail->SMTPAuth   = $mailConfig['smtp_auth'];
        $mail->Username   = $mailConfig['smtp_user'];
        $mail->Password   = $mailConfig['smtp_pass'];
        $mail->SMTPSecure = $mailConfig['smtp_secure'] === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $mailConfig['smtp_port'];
        
        // Recipients
        $mail->setFrom($mailConfig['from_email'], $mailConfig['from_name']);
        $mail->addAddress($toEmail);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $bodyHTML;
        $mail->AltBody = $bodyText ?? strip_tags($bodyHTML);
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("PHPMailer error: " . $mail->ErrorInfo);
        return false;
    }
}

/**
 * Generate sequential Custom Employee ID (e.g. EMP-2026-0002)
 */
function generate_next_employee_id(): string {
    $pdo = get_db_connection();
    $year = date('Y');
    $prefix = "EMP-{$year}-";
    
    // Find latest employee custom id for the current year
    $stmt = $pdo->prepare("SELECT employee_custom_id FROM employees WHERE employee_custom_id LIKE :prefix ORDER BY id DESC LIMIT 1");
    $stmt->execute(['prefix' => $prefix . '%']);
    $latest = $stmt->fetchColumn();
    
    if ($latest) {
        // Extract sequence number from e.g. EMP-2026-0005
        $parts = explode('-', $latest);
        $seq = (int)end($parts);
        $nextSeq = $seq + 1;
    } else {
        $nextSeq = 1;
    }
    
    return $prefix . str_pad((string)$nextSeq, 4, '0', STR_PAD_LEFT);
}

/**
 * Generate verification logs
 */
function log_verification_scan(int $employeeId): bool {
    try {
        $pdo = get_db_connection();
        $stmt = $pdo->prepare("INSERT INTO verification_logs (employee_id, ip_address, user_agent) VALUES (:employee_id, :ip_address, :user_agent)");
        return $stmt->execute([
            'employee_id' => $employeeId,
            'ip_address'  => get_client_ip(),
            'user_agent'  => get_client_user_agent()
        ]);
} catch (Exception $e) {
        error_log("Failed to log verification scan: " . $e->getMessage());
        return false;
    }
}

/**
 * Send an email using a template from the database
 */
function send_templated_email(string $templateName, string $toEmail, array $variables): bool {
    try {
        $pdo = get_db_connection();
        $stmt = $pdo->prepare("SELECT subject, body FROM email_templates WHERE name = :name AND is_active = 1 LIMIT 1");
        $stmt->execute(['name' => $templateName]);
        $template = $stmt->fetch();
        
        if (!$template) {
            error_log("Template not found or inactive: " . $templateName);
            return false;
        }
        
        $subject = $template['subject'];
        $body = $template['body'];
        
        // Replace variables
        foreach ($variables as $key => $value) {
            $placeholder = '{' . $key . '}';
            $subject = str_replace($placeholder, (string)$value, $subject);
            $body = str_replace($placeholder, (string)$value, $body);
        }
        
        // Send email
        $status = send_notification_email($toEmail, $subject, $body);
        
        // Log it
        $logStmt = $pdo->prepare("INSERT INTO email_logs (to_email, subject, status, error_message) VALUES (?, ?, ?, ?)");
        $logStmt->execute([$toEmail, $subject, $status ? 'Sent' : 'Failed', $status ? null : 'Failed to send via PHPMailer']);
        
        return $status;
    } catch (Exception $e) {
        error_log("Error sending templated email: " . $e->getMessage());
        return false;
    }
}
