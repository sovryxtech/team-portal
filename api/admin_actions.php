<?php
declare(strict_types=1);

/**
 * Admin Action Handler API Endpoint
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../src/Controller/RegistrationController.php';
require_once __DIR__ . '/../src/Controller/EmployeeController.php';
require_once __DIR__ . '/../src/Controller/AdminController.php';

// Enforce auth & role
if (!auth_check() || !auth_has_role(['Super Admin', 'Admin/HR'])) {
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

switch ($action) {
    case 'approve_registration':
        $reqId = (int)($_POST['request_id'] ?? 0);
        $approvalParams = [
            'employee_custom_id' => trim($_POST['employee_custom_id'] ?? ''),
            'company_id'         => (int)($_POST['company_id'] ?? 1),
            'branch_id'          => (int)($_POST['branch_id'] ?? 0),
            'department_id'      => (int)($_POST['department_id'] ?? 0),
            'designation_id'     => (int)($_POST['designation_id'] ?? 0),
            'role_id'            => (int)($_POST['role_id'] ?? 3),
            'employment_type'    => trim($_POST['employment_type'] ?? 'Full-time'),
            'joining_date'       => trim($_POST['joining_date'] ?? date('Y-m-d'))
        ];
        
        if (empty($approvalParams['employee_custom_id']) || empty($approvalParams['branch_id']) || empty($approvalParams['department_id']) || empty($approvalParams['designation_id'])) {
            echo json_encode(['success' => false, 'message' => 'All employee mapping fields are required.']);
            exit;
        }

        $regController = new \Src\Controller\RegistrationController();
        $res = $regController->approve($reqId, $approvalParams, (int)$currentUser['id']);
        echo json_encode($res);
        break;

    case 'reject_registration':
        $reqId = (int)($_POST['request_id'] ?? 0);
        $reason = trim($_POST['rejection_reason'] ?? '');
        
        $regController = new \Src\Controller\RegistrationController();
        $res = $regController->reject($reqId, $reason, (int)$currentUser['id']);
        echo json_encode($res);
        break;

    case 'update_employee_status':
        $empId = (int)($_POST['employee_id'] ?? 0);
        $empStatus = $_POST['employment_status'] ?? 'Active';
        $userStatus = $_POST['user_status'] ?? 'Active';
        
        $empController = new \Src\Controller\EmployeeController();
        $res = $empController->updateStatus($empId, $empStatus, $userStatus, (int)$currentUser['id']);
        echo json_encode($res);
        break;

    case 'verify_document':
        $docId = (int)($_POST['document_id'] ?? 0);
        $status = $_POST['status'] ?? 'Verified';
        
        $empController = new \Src\Controller\EmployeeController();
        $res = $empController->verifyDocument($docId, $status, (int)$currentUser['id']);
        echo json_encode($res);
        break;

    // Org CRUDS
    case 'company_create':
    case 'company_update':
    case 'company_delete':
        $adminController = new \Src\Controller\AdminController();
        $subAction = explode('_', $action)[1];
        $res = $adminController->manageCompany($subAction, $_POST);
        echo json_encode($res);
        break;

    case 'branch_create':
    case 'branch_update':
    case 'branch_delete':
        $adminController = new \Src\Controller\AdminController();
        $subAction = explode('_', $action)[1];
        $res = $adminController->manageBranch($subAction, $_POST);
        echo json_encode($res);
        break;

    case 'department_create':
    case 'department_update':
    case 'department_delete':
        $adminController = new \Src\Controller\AdminController();
        $subAction = explode('_', $action)[1];
        $res = $adminController->manageDepartment($subAction, $_POST);
        echo json_encode($res);
        break;

    case 'designation_create':
    case 'designation_update':
    case 'designation_delete':
        $adminController = new \Src\Controller\AdminController();
        $subAction = explode('_', $action)[1];
        $res = $adminController->manageDesignation($subAction, $_POST);
        echo json_encode($res);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action specifier.']);
        break;
}
