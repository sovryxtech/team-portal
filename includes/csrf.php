<?php
declare(strict_types=1);

/**
 * Cross-Site Request Forgery (CSRF) Protection Helpers
 */

require_once __DIR__ . '/auth.php';

/**
 * Generate a new CSRF token and store it in session
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Output a hidden input field containing the CSRF token
 */
function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token()) . '">';
}

/**
 * Validate a request token against session
 */
function csrf_verify(array|null $source = null): bool {
    if (empty($_SESSION['csrf_token'])) {
        return false;
    }
    
    $source = $source ?? $_POST;
    
    // Check $_POST first, then check custom header if applicable
    $token = $source['csrf_token'] ?? '';
    
    if (empty($token) && isset($_SERVER['HTTP_X_CSRF_TOKEN'])) {
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'];
    }
    
    if (empty($token)) {
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Enforce CSRF protection and terminate with 403 if invalid
 */
function csrf_enforce(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!csrf_verify()) {
            http_response_code(403);
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'CSRF verification failed. Request blocked.']);
            } else {
                die("CSRF verification failed. Go back, refresh the page, and try again.");
            }
            exit;
        }
    }
}
