<?php
declare(strict_types=1);

namespace Src\Controller;

require_once __DIR__ . '/../../includes/db_connection.php';
require_once __DIR__ . '/../../includes/utils.php';

/**
 * Controller to handle Public Employee Verification queries
 */
class VerificationController {
    /**
     * Resolve employee data by custom ID and log verification scan
     */
    public function verifyEmployee(string $customId): array|null {
        if (empty($customId)) {
            return null;
        }

        try {
            $pdo = get_db_connection();
            
            // Query employee details including company info
            $stmt = $pdo->prepare("
                SELECT e.id as employee_id, e.employee_custom_id, e.joining_date, e.employment_status,
                       ep.full_name, ep.profile_photo,
                       c.name as company_name, b.name as branch_name,
                       d.name as department_name, dg.title as designation_title
                FROM employees e
                JOIN employee_profiles ep ON e.id = ep.employee_id
                JOIN companies c ON e.company_id = c.id
                JOIN branches b ON e.branch_id = b.id
                JOIN departments d ON e.department_id = d.id
                JOIN designations dg ON e.designation_id = dg.id
                WHERE e.employee_custom_id = :custom_id
                LIMIT 1
            ");
            $stmt->execute(['custom_id' => $customId]);
            $employee = $stmt->fetch();

            if (!$employee) {
                return null;
            }

            // Log verification scan dynamically
            log_verification_scan((int)$employee['employee_id']);

            return $employee;
        } catch (\Exception $e) {
            error_log("Verification lookup failed: " . $e->getMessage());
            return null;
        }
    }
}
