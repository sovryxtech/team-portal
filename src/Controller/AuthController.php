<?php
declare(strict_types=1);

namespace Src\Controller;

require_once __DIR__ . '/../../includes/db_connection.php';
require_once __DIR__ . '/../../includes/auth.php';

/**
 * Controller for User Authentication
 */
class AuthController {
    /**
     * Authenticate user with credentials and CAPTCHA validation
     */
    public function login(string $username, string $password, string $captchaInput): array {
        // Validate CAPTCHA
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $sessionCaptcha = $_SESSION['captcha_phrase'] ?? '';
        unset($_SESSION['captcha_phrase']); // Clear captcha immediately
        
        if (empty($captchaInput) || strtolower($captchaInput) !== strtolower($sessionCaptcha)) {
            return ['success' => false, 'message' => 'Invalid security code (CAPTCHA). Please try again.'];
        }

        try {
            $pdo = get_db_connection();
            
            // Fetch user details
            $stmt = $pdo->prepare("SELECT id, username, password_hash, email, role_id, status FROM users WHERE username = :username OR email = :username LIMIT 1");
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch();

            if (!$user) {
                // Return generic error to prevent enumeration
                return ['success' => false, 'message' => 'Invalid username/email or password.'];
            }

            if ($user['status'] !== 'Active') {
                return ['success' => false, 'message' => 'Your account is currently inactive. Please contact HR.'];
            }

            // Verify Password
            if (!password_verify($password, $user['password_hash'])) {
                return ['success' => false, 'message' => 'Invalid username/email or password.'];
            }

            // Log in the user (sets sessions, logs login)
            auth_login($user);

            return ['success' => true, 'message' => 'Login successful.', 'role' => $_SESSION['role_name']];
        } catch (\Exception $e) {
            error_log("Login error: " . $e->getMessage());
            return ['success' => false, 'message' => 'A system error occurred during login. Please try again.'];
        }
    }

    /**
     * Terminate user session
     */
    public function logout(): void {
        auth_logout();
    }
}
