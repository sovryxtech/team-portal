<?php
declare(strict_types=1);

/**
 * API Handler for Registration Submissions
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../src/Controller/RegistrationController.php';

// Enforce POST and CSRF
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

$controller = new \Src\Controller\RegistrationController();
$result = $controller->submit($_POST, $_FILES);

echo json_encode($result);
