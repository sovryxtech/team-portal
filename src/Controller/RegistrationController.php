<?php
declare(strict_types=1);

namespace Src\Controller;

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_connection.php';
require_once __DIR__ . '/../../includes/utils.php';
require_once __DIR__ . '/../Service/DocumentManager.php';

use Src\Service\DocumentManager;

/**
 * Controller to manage Online Registration Requests & Approvals
 */
class RegistrationController {
    private DocumentManager $docManager;

    public function __construct() {
        $this->docManager = new DocumentManager();
    }

    /**
     * Submit a new registration request
     */
    public function submit(array $postData, array $filesData): array {
        // Validate Captcha
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $captchaInput = $postData['captcha_phrase'] ?? '';
        $sessionCaptcha = $_SESSION['captcha_phrase'] ?? '';
        unset($_SESSION['captcha_phrase']); // Clear captcha
        
        if (empty($captchaInput) || strtolower($captchaInput) !== strtolower($sessionCaptcha)) {
            return ['success' => false, 'message' => 'Invalid CAPTCHA security code.'];
        }

        // Verify Email OTP
        if (empty($_SESSION['email_verified']) || $_SESSION['email_verified'] !== $postData['email']) {
            return ['success' => false, 'message' => 'Email address has not been verified. Please verify your email first.'];
        }

        // Combine Names
        $firstName = trim($postData['first_name'] ?? '');
        $middleName = trim($postData['middle_name'] ?? '');
        $lastName = trim($postData['last_name'] ?? '');
        $fullName = trim($firstName . ' ' . $middleName . ' ' . $lastName);
        $postData['full_name'] = preg_replace('/\s+/', ' ', $fullName); // Fix double spaces if no middle name

        // Combine Address
        $currentAddr = trim($postData['address'] ?? '');
        $permAddr = trim($postData['permanent_address'] ?? '');
        if (!empty($permAddr)) {
            $postData['address'] = json_encode(['current' => $currentAddr, 'permanent' => $permAddr]);
        }

        // Basic verification of required fields
        $required = ['first_name', 'last_name', 'email', 'phone', 'dob', 'gender', 'username', 'password', 'address'];
        foreach ($required as $field) {
            if (empty($postData[$field]) && $field !== 'address') { // we handled address manually above
                return ['success' => false, 'message' => "Field '" . str_replace('_', ' ', $field) . "' is required."];
            }
        }

        try {
            $pdo = get_db_connection();
            
            // Check username & email availability
            $checkStmt = $pdo->prepare("SELECT id FROM users WHERE username = :username OR email = :email LIMIT 1");
            $checkStmt->execute(['username' => $postData['username'], 'email' => $postData['email']]);
            if ($checkStmt->fetch()) {
                return ['success' => false, 'message' => 'Username or email already exists in the system.'];
            }

            // Check if email already has pending registration request
            $checkPendingStmt = $pdo->prepare("SELECT id FROM registration_requests WHERE status = 'Pending' AND JSON_UNQUOTE(JSON_EXTRACT(form_data_json, '$.email')) = :email LIMIT 1");
            $checkPendingStmt->execute(['email' => $postData['email']]);
            if ($checkPendingStmt->fetch()) {
                return ['success' => false, 'message' => 'A pending registration request already exists for this email address.'];
            }

            // Upload Files
            $uploadedFiles = [];
            
            // 1. Passport Photo
            if (!empty($filesData['profile_photo']['name'])) {
                $uploadedFiles['profile_photo'] = $this->docManager->uploadProfilePhoto($filesData['profile_photo']);
            } else {
                return ['success' => false, 'message' => 'Profile passport photo is required.'];
            }

            // 2. Mandatory documents
            $requiredDocs = ['citizenship', 'cv', 'certificates', 'police_clearance'];
            foreach ($requiredDocs as $docType) {
                if (!empty($filesData[$docType]['name'])) {
                    $uploadedFiles[$docType] = $this->docManager->uploadDocument($filesData[$docType]);
                } else {
                    return ['success' => false, 'message' => "Document '" . strtoupper($docType) . "' is required."];
                }
            }

            // 3. Optional documents
            $optionalDocs = ['appointment_letter', 'pan_card', 'experience_certificate'];
            foreach ($optionalDocs as $docType) {
                if (!empty($filesData[$docType]['name'])) {
                    $uploadedFiles[$docType] = $this->docManager->uploadDocument($filesData[$docType]);
                }
            }

            // Hash password prior to storage
            $postData['password_hash'] = password_hash($postData['password'], PASSWORD_BCRYPT);
            unset($postData['password']); // Clear plain text password
            unset($postData['captcha_phrase']);

            // Insert registration request
            $stmt = $pdo->prepare("INSERT INTO registration_requests (form_data_json, file_paths_json, status) VALUES (:form_data, :file_paths, 'Pending')");
            $stmt->execute([
                'form_data'  => json_encode($postData),
                'file_paths' => json_encode($uploadedFiles)
            ]);

            // Notify Admin via email
            $subject = "New Employee Registration Request Pending Approval";
            $adminBody = "<h3>A new employee registration request has been submitted.</h3>
                          <p><strong>Applicant Name:</strong> " . htmlspecialchars($postData['full_name']) . "</p>
                          <p><strong>Email:</strong> " . htmlspecialchars($postData['email']) . "</p>
                          <p>Please log in to the HR Portal to approve or reject this applicant.</p>";
            
            // Get Admin Emails
            $adminEmailsStmt = $pdo->query("SELECT email FROM users WHERE role_id = 1 OR role_id = 2");
            while ($adminEmail = $adminEmailsStmt->fetchColumn()) {
                send_notification_email($adminEmail, $subject, $adminBody);
            }

            // Log activity
            log_activity(null, 'Employee registration request submitted', "Applicant Name: {$postData['full_name']}, Email: {$postData['email']}");

            return ['success' => true, 'message' => 'Registration request submitted successfully. It is pending review.'];

        } catch (\Exception $e) {
            error_log("Registration submit error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()];
        }
    }

    /**
     * Approve registration request and provision employee account
     */
    public function approve(int $requestId, array $approvalParams, int $adminUserId): array {
        try {
            $pdo = get_db_connection();
            $pdo->beginTransaction();

            // Fetch registration details
            $stmt = $pdo->prepare("SELECT * FROM registration_requests WHERE id = :id FOR UPDATE");
            $stmt->execute(['id' => $requestId]);
            $request = $stmt->fetch();

            if (!$request || $request['status'] !== 'Pending') {
                return ['success' => false, 'message' => 'Request not found or already processed.'];
            }

            $formData = json_decode($request['form_data_json'], true);
            $filePaths = json_decode($request['file_paths_json'], true);

            // Double check username and email availability
            $checkStmt = $pdo->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
            $checkStmt->execute(['username' => $formData['username'], 'email' => $formData['email']]);
            if ($checkStmt->fetch()) {
                return ['success' => false, 'message' => 'Username or Email is already taken by another account.'];
            }

            // Create User Account
            $userStmt = $pdo->prepare("INSERT INTO users (role_id, username, password_hash, email, status) VALUES (:role_id, :username, :password_hash, :email, 'Active')");
            $userStmt->execute([
                'role_id'       => $approvalParams['role_id'],
                'username'      => $formData['username'],
                'password_hash' => $formData['password_hash'],
                'email'         => $formData['email']
            ]);
            $userId = (int)$pdo->lastInsertId();

            // Create Employee Record
            $empStmt = $pdo->prepare("INSERT INTO employees (user_id, employee_custom_id, company_id, branch_id, department_id, designation_id, employment_type, joining_date, employment_status) VALUES (:user_id, :employee_custom_id, :company_id, :branch_id, :department_id, :designation_id, :employment_type, :joining_date, 'Active')");
            $empStmt->execute([
                'user_id'            => $userId,
                'employee_custom_id' => $approvalParams['employee_custom_id'],
                'company_id'         => $approvalParams['company_id'],
                'branch_id'          => $approvalParams['branch_id'],
                'department_id'      => $approvalParams['department_id'],
                'designation_id'     => $approvalParams['designation_id'],
                'employment_type'    => $approvalParams['employment_type'],
                'joining_date'       => $approvalParams['joining_date']
            ]);
            $employeeId = (int)$pdo->lastInsertId();

            // Assemble emergency contact
            $emergencyContact = [
                'name'     => $formData['emergency_name'] ?? '',
                'relation' => $formData['emergency_relation'] ?? '',
                'phone'    => $formData['emergency_phone'] ?? ''
            ];

            // Create Employee Profile
            $profileStmt = $pdo->prepare("INSERT INTO employee_profiles (employee_id, full_name, profile_photo, dob, gender, blood_group, nationality, marital_status, phone, address, emergency_contact) VALUES (:employee_id, :full_name, :profile_photo, :dob, :gender, :blood_group, :nationality, :marital_status, :phone, :address, :emergency_contact)");
            $profileStmt->execute([
                'employee_id'       => $employeeId,
                'full_name'         => $formData['full_name'],
                'profile_photo'     => $filePaths['profile_photo'] ?? null,
                'dob'               => $formData['dob'] ?? null,
                'gender'            => $formData['gender'] ?? null,
                'blood_group'       => $formData['blood_group'] ?? null,
                'nationality'       => $formData['nationality'] ?? null,
                'marital_status'    => $formData['marital_status'] ?? 'Single',
                'phone'             => $formData['phone'] ?? null,
                'address'           => $formData['address'] ?? null,
                'emergency_contact' => json_encode($emergencyContact)
            ]);

            // Save Documents
            $docStmt = $pdo->prepare("INSERT INTO employee_documents (employee_id, document_type, file_path, status, verified_at) VALUES (:employee_id, :document_type, :file_path, 'Verified', CURRENT_TIMESTAMP)");
            foreach ($filePaths as $type => $path) {
                if ($type === 'profile_photo') continue; // Handled in profile
                $docStmt->execute([
                    'employee_id'   => $employeeId,
                    'document_type' => ucwords(str_replace('_', ' ', $type)),
                    'file_path'     => $path
                ]);
            }

            // Update Request Status
            $reqStmt = $pdo->prepare("UPDATE registration_requests SET status = 'Approved' WHERE id = :id");
            $reqStmt->execute(['id' => $requestId]);

            $pdo->commit();

            // Send Welcome Email
            $templateVars = [
                'employee_name' => $formData['full_name'],
                'employee_id' => $approvalParams['employee_custom_id'],
                'username' => $formData['username'],
                'login_url' => get_base_url()
            ];
            
            if (!send_templated_email('Welcome Email', $formData['email'], $templateVars)) {
                // Fallback
                $subject = "Welcome to Sovryx Tech Portal - Account Active";
                $welcomeBody = "<h3>Dear " . htmlspecialchars($formData['full_name']) . ",</h3>
                                <p>We are pleased to inform you that your registration has been approved.</p>
                                <p><strong>Your Employee ID:</strong> " . htmlspecialchars($approvalParams['employee_custom_id']) . "</p>
                                <p><strong>Login Username:</strong> " . htmlspecialchars($formData['username']) . "</p>
                                <p>You can now log in to the portal using your credentials to view your Digital ID and upload files.</p>
                                <p>Best Regards,<br>Sovryx Tech Team</p>";
                send_notification_email($formData['email'], $subject, $welcomeBody);
            }

            // Log activity
            log_activity($adminUserId, 'Approved registration request', "Approved Request ID: {$requestId}, Assigned Employee ID: {$approvalParams['employee_custom_id']}");

            return ['success' => true, 'message' => 'Registration approved, and employee account has been created.'];

        } catch (\Exception $e) {
            $pdo->rollBack();
            error_log("Registration approval error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Approval failed: ' . $e->getMessage()];
        }
    }

    /**
     * Reject registration request
     */
    public function reject(int $requestId, string $reason, int $adminUserId): array {
        if (empty($reason)) {
            return ['success' => false, 'message' => 'Rejection reason is required.'];
        }

        try {
            $pdo = get_db_connection();
            
            $stmt = $pdo->prepare("SELECT * FROM registration_requests WHERE id = :id FOR UPDATE");
            $stmt->execute(['id' => $requestId]);
            $request = $stmt->fetch();

            if (!$request || $request['status'] !== 'Pending') {
                return ['success' => false, 'message' => 'Request not found or already processed.'];
            }

            $formData = json_decode($request['form_data_json'], true);

            // Update registration requests
            $updateStmt = $pdo->prepare("UPDATE registration_requests SET status = 'Rejected', rejection_reason = :reason WHERE id = :id");
            $updateStmt->execute(['id' => $requestId, 'reason' => $reason]);

            // Dispatch rejection notification
            $templateVars = [
                'employee_name' => $formData['full_name'],
                'reason' => $reason
            ];
            if (!send_templated_email('Registration Rejected', $formData['email'], $templateVars)) {
                // Fallback
                $subject = "Registration Request Update - Rejected";
                $rejectionBody = "<h3>Dear " . htmlspecialchars($formData['full_name']) . ",</h3>
                                  <p>Thank you for submitting your application to Sovryx Tech.</p>
                                  <p>Unfortunately, your registration request has been rejected for the following reason:</p>
                                  <blockquote style='border-left:3px solid #ff0000; padding-left:10px; font-style:italic;'>
                                    " . htmlspecialchars($reason) . "
                                  </blockquote>
                                  <p>If you believe this was an error, please submit a new application with corrected details.</p>
                                  <p>Best Regards,<br>HR Department</p>";
                send_notification_email($formData['email'], $subject, $rejectionBody);
            }

            // Log activity
            log_activity($adminUserId, 'Rejected registration request', "Rejected Request ID: {$requestId}. Reason: {$reason}");

            return ['success' => true, 'message' => 'Registration request has been rejected.'];
        } catch (\Exception $e) {
            error_log("Registration rejection error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Rejection failed: ' . $e->getMessage()];
        }
    }
}
