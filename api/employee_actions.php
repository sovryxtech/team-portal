<?php
declare(strict_types=1);

/**
 * Employee Action Handler API Endpoint
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../src/Controller/EmployeeController.php';

// Enforce auth & role
if (!auth_check() || !auth_has_role('Employee')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized action.']);
    exit;
}

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

$action = $_POST['action'] ?? '';
$currentUser = auth_user();
$employeeId = (int)$currentUser['employee_id'];

switch ($action) {
    case 'update_contact_info':
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $emergencyContact = [
            'name'     => trim($_POST['emergency_name'] ?? ''),
            'relation' => trim($_POST['emergency_relation'] ?? ''),
            'phone'    => trim($_POST['emergency_phone'] ?? '')
        ];
        
        $controller = new \Src\Controller\EmployeeController();
        $res = $controller->updateContactInfo($employeeId, $phone, $address, $emergencyContact, (int)$currentUser['id']);
        echo json_encode($res);
        break;

    case 'upload_additional_document':
        $docType = trim($_POST['document_type'] ?? '');
        
        if (empty($_FILES['document_file']['name'])) {
            echo json_encode(['success' => false, 'message' => 'Please select a document file to upload.']);
            exit;
        }
        
        $controller = new \Src\Controller\EmployeeController();
        $res = $controller->uploadDocument($employeeId, $docType, $_FILES['document_file'], (int)$currentUser['id']);
        echo json_encode($res);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action specifier.']);
        break;
}
