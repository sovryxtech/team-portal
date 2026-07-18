<?php
declare(strict_types=1);

namespace Src\Controller;

require_once __DIR__ . '/../../includes/db_connection.php';
require_once __DIR__ . '/../../includes/utils.php';
require_once __DIR__ . '/../Service/DocumentManager.php';

use Src\Service\DocumentManager;

/**
 * Controller to manage Employee Profiles, Records, and Documents
 */
class EmployeeController {
    private DocumentManager $docManager;

    public function __construct() {
        $this->docManager = new DocumentManager();
    }

    /**
     * Fetch complete employee profile details
     */
    public function getDetails(int $employeeId): array|null {
        $pdo = get_db_connection();
        $stmt = $pdo->prepare("
            SELECT e.*, ep.*, u.username, u.email, u.status as user_status,
                   c.name as company_name, b.name as branch_name,
                   d.name as department_name, dg.title as designation_title
            FROM employees e
            JOIN employee_profiles ep ON e.id = ep.employee_id
            JOIN users u ON e.user_id = u.id
            JOIN companies c ON e.company_id = c.id
            JOIN branches b ON e.branch_id = b.id
            JOIN departments d ON e.department_id = d.id
            JOIN designations dg ON e.designation_id = dg.id
            WHERE e.id = :employee_id
            LIMIT 1
        ");
        $stmt->execute(['employee_id' => $employeeId]);
        $details = $stmt->fetch();
        return $details ?: null;
    }

    /**
     * Fetch employee profile details by user ID
     */
    public function getDetailsByUserId(int $userId): array|null {
        $pdo = get_db_connection();
        $stmt = $pdo->prepare("SELECT id FROM employees WHERE user_id = :user_id LIMIT 1");
        $stmt->execute(['user_id' => $userId]);
        $empId = $stmt->fetchColumn();
        return $empId ? $this->getDetails((int)$empId) : null;
    }

    /**
     * Fetch all employee documents
     */
    public function getDocuments(int $employeeId): array {
        $pdo = get_db_connection();
        $stmt = $pdo->prepare("SELECT * FROM employee_documents WHERE employee_id = :employee_id");
        $stmt->execute(['employee_id' => $employeeId]);
        return $stmt->fetchAll();
    }

    /**
     * Update employee contact details (Employee self-service)
     */
    public function updateContactInfo(int $employeeId, string $phone, string $address, array $emergencyContact, int $userId): array {
        if (empty($phone) || empty($address)) {
            return ['success' => false, 'message' => 'Phone and Address cannot be empty.'];
        }

        try {
            $pdo = get_db_connection();
            $stmt = $pdo->prepare("
                UPDATE employee_profiles 
                SET phone = :phone, address = :address, emergency_contact = :emergency 
                WHERE employee_id = :employee_id
            ");
            $stmt->execute([
                'phone'       => $phone,
                'address'     => $address,
                'emergency'   => json_encode($emergencyContact),
                'employee_id' => $employeeId
            ]);

            log_activity($userId, 'Updated profile contact info', "Employee ID: {$employeeId}");
            return ['success' => true, 'message' => 'Contact details updated successfully.'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Failed to update: ' . $e->getMessage()];
        }
    }

    /**
     * Upload additional document for validation
     */
    public function uploadDocument(int $employeeId, string $docType, array $fileData, int $userId): array {
        if (empty($docType)) {
            return ['success' => false, 'message' => 'Document type is required.'];
        }

        try {
            $path = $this->docManager->uploadDocument($fileData);
            
            $pdo = get_db_connection();
            $stmt = $pdo->prepare("
                INSERT INTO employee_documents (employee_id, document_type, file_path, status) 
                VALUES (:employee_id, :doc_type, :file_path, 'Pending')
            ");
            $stmt->execute([
                'employee_id' => $employeeId,
                'doc_type'    => $docType,
                'file_path'   => $path
            ]);

            log_activity($userId, 'Uploaded employee document', "Type: {$docType}, File: {$path}");
            return ['success' => true, 'message' => 'Document uploaded successfully and is pending verification.'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Upload failed: ' . $e->getMessage()];
        }
    }

    /**
     * Verify or Reject document (Admin operation)
     */
    public function verifyDocument(int $documentId, string $status, int $adminUserId): array {
        if (!in_array($status, ['Verified', 'Rejected'], true)) {
            return ['success' => false, 'message' => 'Invalid status option.'];
        }

        try {
            $pdo = get_db_connection();
            
            // Check document ownership/details
            $stmt = $pdo->prepare("SELECT d.*, ep.full_name, u.email FROM employee_documents d JOIN employees e ON d.employee_id = e.id JOIN employee_profiles ep ON e.id = ep.employee_id JOIN users u ON e.user_id = u.id WHERE d.id = :id");
            $stmt->execute(['id' => $documentId]);
            $doc = $stmt->fetch();
            
            if (!$doc) {
                return ['success' => false, 'message' => 'Document record not found.'];
            }

            $update = $pdo->prepare("UPDATE employee_documents SET status = :status, verified_at = CURRENT_TIMESTAMP WHERE id = :id");
            $update->execute(['status' => $status, 'id' => $documentId]);

            // Notify user
            $subject = "Document Verification Status Update - " . $status;
            $body = "<h3>Dear " . htmlspecialchars($doc['full_name']) . ",</h3>
                     <p>Your uploaded document <strong>" . htmlspecialchars($doc['document_type']) . "</strong> has been marked as <strong>" . $status . "</strong> by HR.</p>
                     <p>Best Regards,<br>HR Department</p>";
            send_notification_email($doc['email'], $subject, $body);

            log_activity($adminUserId, 'Modified employee document status', "Document ID: {$documentId}, Status: {$status}");
            return ['success' => true, 'message' => "Document successfully marked as {$status}."];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Database operation failed: ' . $e->getMessage()];
        }
    }

    /**
     * Update Employee Status (Admin operation)
     */
    public function updateStatus(int $employeeId, string $empStatus, string $userStatus, int $adminUserId): array {
        try {
            $pdo = get_db_connection();
            $pdo->beginTransaction();

            // Fetch user ID
            $stmt = $pdo->prepare("SELECT user_id FROM employees WHERE id = :id");
            $stmt->execute(['id' => $employeeId]);
            $userId = $stmt->fetchColumn();

            if (!$userId) {
                return ['success' => false, 'message' => 'Employee not found.'];
            }

            // Update user status
            $userStmt = $pdo->prepare("UPDATE users SET status = :status WHERE id = :id");
            $userStmt->execute(['status' => $userStatus, 'id' => $userId]);

            // Update employee employment status
            $empStmt = $pdo->prepare("UPDATE employees SET employment_status = :status WHERE id = :id");
            $empStmt->execute(['status' => $empStatus, 'id' => $employeeId]);

            $pdo->commit();

            log_activity($adminUserId, 'Updated employee status', "Employee ID: {$employeeId}, Employment: {$empStatus}, Account: {$userStatus}");
            return ['success' => true, 'message' => 'Status updated successfully.'];
        } catch (\Exception $e) {
            $pdo->rollBack();
            return ['success' => false, 'message' => 'Status update failed: ' . $e->getMessage()];
        }
    }

    /**
     * Submit a support ticket
     */
    public function createSupportTicket(int $userId, array $data, ?array $file = null): array {
        try {
            $pdo = get_db_connection();
            
            // Handle optional attachment upload
            $attachmentPath = null;
            if ($file && $file['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../uploads/tickets/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9.\-_]/', '', basename($file['name']));
                $targetPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                    $attachmentPath = 'uploads/tickets/' . $fileName;
                }
            }
            
            // Generate ticket number
            $ticketNumber = 'TCK-' . date('Y') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));

            $stmt = $pdo->prepare("
                INSERT INTO support_tickets (user_id, ticket_number, category, subject, description, attachment) 
                VALUES (:user_id, :ticket_number, :category, :subject, :description, :attachment)
            ");
            
            $stmt->execute([
                'user_id' => $userId,
                'ticket_number' => $ticketNumber,
                'category' => $data['category'],
                'subject' => $data['subject'],
                'description' => $data['description'],
                'attachment' => $attachmentPath
            ]);
            
            log_activity($userId, 'Submitted Support Ticket', "Ticket Number: {$ticketNumber}");
            
            return ['success' => true, 'message' => 'Ticket submitted successfully!'];
        } catch (\PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    /**
     * Get tickets for a user
     */
    public function getSupportTickets(int $userId): array {
        try {
            $pdo = get_db_connection();
            $stmt = $pdo->prepare("SELECT * FROM support_tickets WHERE user_id = :user_id ORDER BY created_at DESC");
            $stmt->execute(['user_id' => $userId]);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            return [];
        }
    }
}
