<?php
declare(strict_types=1);

/**
 * Authentication and Role-Based Access Control (RBAC) System
 */

require_once __DIR__ . '/db_connection.php';
require_once __DIR__ . '/utils.php';

// Configure session options for high security
if (session_status() === PHP_SESSION_NONE) {
    $cookieParams = [
        'lifetime' => 3600,
        'path' => '/',
        'domain' => '',
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
        'httponly' => true,
        'samesite' => 'Lax'
    ];
    
    session_set_cookie_params($cookieParams);
    
    // Change session name to custom configured one
    $appConfig = require __DIR__ . '/../config/app.php';
    session_name($appConfig['session_name'] ?? 'SOVRYX_PORTAL_SESSION');
    
    session_start();
}

/**
 * Log in a user and set up session
 */
function auth_login(array $user): void {
    // Prevent session hijacking by regenerating ID
    session_regenerate_id(true);
    
    $_SESSION['user_id'] = (int)$user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role_id'] = (int)$user['role_id'];
    
    // Fetch role name & permissions
    $pdo = get_db_connection();
    $stmt = $pdo->prepare("SELECT name, permissions FROM roles WHERE id = :id");
    $stmt->execute(['id' => $user['role_id']]);
    $role = $stmt->fetch();
    
    $_SESSION['role_name'] = $role['name'] ?? 'Employee';
    $_SESSION['permissions'] = json_decode($role['permissions'] ?? '{}', true);
    
    // Check if employee profile is attached
    if ($_SESSION['role_name'] === 'Employee') {
        $empStmt = $pdo->prepare("SELECT e.id as employee_id, ep.full_name, ep.profile_photo FROM employees e JOIN employee_profiles ep ON e.id = ep.employee_id WHERE e.user_id = :user_id");
        $empStmt->execute(['user_id' => $user['id']]);
        $emp = $empStmt->fetch();
        if ($emp) {
            $_SESSION['employee_id'] = (int)$emp['employee_id'];
            $_SESSION['full_name'] = $emp['full_name'];
            $_SESSION['profile_photo'] = $emp['profile_photo'];
        }
    } else {
        $_SESSION['full_name'] = 'Administrator';
        $_SESSION['profile_photo'] = null;
    }
    
    // Update last login
    $updateStmt = $pdo->prepare("UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = :id");
    $updateStmt->execute(['id' => $user['id']]);
    
    // Log login
    log_login((int)$user['id']);
    log_activity((int)$user['id'], 'User login', 'Successfully logged in');
}

/**
 * Log out a user and destroy session
 */
function auth_logout(): void {
    if (isset($_SESSION['user_id'])) {
        log_activity($_SESSION['user_id'], 'User logout', 'Successfully logged out');
    }
    
    $_SESSION = [];
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
    
    session_destroy();
}

/**
 * Check if the user is authenticated
 */
function auth_check(): bool {
    return isset($_SESSION['user_id']);
}

/**
 * Get current logged in user details from session
 */
function auth_user(): array|null {
    if (!auth_check()) {
        return null;
    }
    return [
        'id'            => $_SESSION['user_id'],
        'username'      => $_SESSION['username'],
        'email'         => $_SESSION['email'],
        'role_id'       => $_SESSION['role_id'],
        'role_name'     => $_SESSION['role_name'],
        'full_name'     => $_SESSION['full_name'] ?? 'User',
        'profile_photo' => $_SESSION['profile_photo'] ?? null,
        'employee_id'   => $_SESSION['employee_id'] ?? null,
        'permissions'   => $_SESSION['permissions'] ?? []
    ];
}

/**
 * Check if current user has a specific role or matches any in an array of roles
 */
function auth_has_role(string|array $roles): bool {
    if (!auth_check()) {
        return false;
    }
    
    $userRole = $_SESSION['role_name'];
    
    // Super Admin has all privileges implicitly
    if ($userRole === 'Super Admin') {
        return true;
    }
    
    if (is_array($roles)) {
        return in_array($userRole, $roles, true);
    }
    
    return $userRole === $roles;
}

/**
 * Enforce authentication and check roles. Redirect on failure.
 */
function auth_enforce(string|array|null $allowedRoles = null): void {
    if (!auth_check()) {
        // Store current URL in session for post-login redirect
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        header("Location: " . get_base_url() . "/login.php");
        exit;
    }
    
    if ($allowedRoles !== null) {
        if (!auth_has_role($allowedRoles)) {
            // Log unauthorized attempt
            log_activity($_SESSION['user_id'], 'Unauthorized access attempt', 'Tried accessing page restricted to ' . (is_array($allowedRoles) ? implode(', ', $allowedRoles) : $allowedRoles));
            
            // Redirect to dashboard index or show 403
            http_response_code(403);
            die("Access Denied. You do not have permissions to view this page.");
        }
    }
}

/**
 * Helper to get configured site Base URL
 */
function get_base_url(): string {
    $config = require __DIR__ . '/../config/app.php';
    return rtrim($config['base_url'] ?? 'http://localhost/team-portal', '/');
}
