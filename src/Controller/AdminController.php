<?php
declare(strict_types=1);

namespace Src\Controller;

require_once __DIR__ . '/../../includes/db_connection.php';
require_once __DIR__ . '/../../includes/utils.php';

/**
 * Controller for Admin Operations, Metrics, and Organizational CRUDs
 */
class AdminController {
    /**
     * Get summary metrics for admin dashboard panel
     */
    public function getDashboardMetrics(): array {
        $pdo = get_db_connection();
        
        $metrics = [];
        
        // Total Employees
        $metrics['total_employees'] = (int)$pdo->query("SELECT COUNT(*) FROM employees")->fetchColumn();
        
        // Active Employees
        $metrics['active_employees'] = (int)$pdo->query("SELECT COUNT(*) FROM employees WHERE employment_status = 'Active'")->fetchColumn();
        
        // Pending Registrations
        $metrics['pending_registrations'] = (int)$pdo->query("SELECT COUNT(*) FROM registration_requests WHERE status = 'Pending'")->fetchColumn();
        
        // Total Verification Scans
        $metrics['total_scans'] = (int)$pdo->query("SELECT COUNT(*) FROM verification_logs")->fetchColumn();
        
        // Recent registration request stream
        $stmt = $pdo->query("SELECT id, form_data_json, created_at FROM registration_requests WHERE status = 'Pending' ORDER BY id DESC LIMIT 5");
        $metrics['recent_registrations'] = [];
        while ($row = $stmt->fetch()) {
            $formData = json_decode($row['form_data_json'], true);
            $metrics['recent_registrations'][] = [
                'id'         => $row['id'],
                'full_name'  => $formData['full_name'] ?? 'N/A',
                'email'      => $formData['email'] ?? 'N/A',
                'phone'      => $formData['phone'] ?? 'N/A',
                'created_at' => $row['created_at']
            ];
        }
        
        // Recent activity logs
        $actStmt = $pdo->query("SELECT al.*, u.username FROM activity_logs al LEFT JOIN users u ON al.user_id = u.id ORDER BY al.id DESC LIMIT 10");
        $metrics['recent_activities'] = $actStmt->fetchAll();

        // Department distribution counts for Chart.js
        $deptStmt = $pdo->query("SELECT d.name as dept_name, COUNT(e.id) as emp_count FROM departments d LEFT JOIN employees e ON d.id = e.department_id GROUP BY d.id");
        $metrics['department_chart'] = $deptStmt->fetchAll();
        
        // Branch distribution counts for Chart.js
        $branchStmt = $pdo->query("SELECT b.name as branch_name, COUNT(e.id) as emp_count FROM branches b LEFT JOIN employees e ON b.id = e.branch_id GROUP BY b.id");
        $metrics['branch_chart'] = $branchStmt->fetchAll();

        return $metrics;
    }

    /**
     * Manage Company Profile CRUD
     */
    public function manageCompany(string $action, array $data): array {
        $pdo = get_db_connection();
        try {
            if ($action === 'create') {
                $stmt = $pdo->prepare("INSERT INTO companies (name, address, contact, email_settings) VALUES (:name, :address, :contact, :email_settings)");
                $stmt->execute([
                    'name'           => $data['name'],
                    'address'        => $data['address'] ?? null,
                    'contact'        => $data['contact'] ?? null,
                    'email_settings' => json_encode($data['email_settings'] ?? [])
                ]);
                return ['success' => true, 'message' => 'Company created successfully.'];
            } elseif ($action === 'update') {
                $stmt = $pdo->prepare("UPDATE companies SET name = :name, address = :address, contact = :contact, email_settings = :email_settings WHERE id = :id");
                $stmt->execute([
                    'id'             => $data['id'],
                    'name'           => $data['name'],
                    'address'        => $data['address'] ?? null,
                    'contact'        => $data['contact'] ?? null,
                    'email_settings' => json_encode($data['email_settings'] ?? [])
                ]);
                return ['success' => true, 'message' => 'Company updated successfully.'];
            } elseif ($action === 'delete') {
                $stmt = $pdo->prepare("DELETE FROM companies WHERE id = :id");
                $stmt->execute(['id' => $data['id']]);
                return ['success' => true, 'message' => 'Company deleted successfully.'];
            }
        } catch (\PDOException $e) {
            if ($e->getCode() === '23000' || (isset($e->errorInfo[1]) && $e->errorInfo[1] === 1451)) {
                return ['success' => false, 'message' => 'Cannot delete this record because it is currently linked to one or more active employees. Please reassign the employees first.'];
            }
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
        return ['success' => false, 'message' => 'Invalid action'];
    }

    /**
     * Manage Branch CRUD
     */
    public function manageBranch(string $action, array $data): array {
        $pdo = get_db_connection();
        try {
            if ($action === 'create') {
                $stmt = $pdo->prepare("INSERT INTO branches (company_id, name, address, contact) VALUES (:company_id, :name, :address, :contact)");
                $stmt->execute([
                    'company_id' => $data['company_id'],
                    'name'       => $data['name'],
                    'address'    => $data['address'] ?? null,
                    'contact'    => $data['contact'] ?? null
                ]);
                return ['success' => true, 'message' => 'Branch created successfully.'];
            } elseif ($action === 'update') {
                $stmt = $pdo->prepare("UPDATE branches SET company_id = :company_id, name = :name, address = :address, contact = :contact WHERE id = :id");
                $stmt->execute([
                    'id'         => $data['id'],
                    'company_id' => $data['company_id'],
                    'name'       => $data['name'],
                    'address'    => $data['address'] ?? null,
                    'contact'    => $data['contact'] ?? null
                ]);
                return ['success' => true, 'message' => 'Branch updated successfully.'];
            } elseif ($action === 'delete') {
                $stmt = $pdo->prepare("DELETE FROM branches WHERE id = :id");
                $stmt->execute(['id' => $data['id']]);
                return ['success' => true, 'message' => 'Branch deleted successfully.'];
            }
        } catch (\PDOException $e) {
            if ($e->getCode() === '23000' || (isset($e->errorInfo[1]) && $e->errorInfo[1] === 1451)) {
                return ['success' => false, 'message' => 'Cannot delete this record because it is currently linked to one or more active employees. Please reassign the employees first.'];
            }
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
        return ['success' => false, 'message' => 'Invalid action'];
    }

    /**
     * Manage Department CRUD
     */
    public function manageDepartment(string $action, array $data): array {
        $pdo = get_db_connection();
        try {
            if ($action === 'create') {
                $stmt = $pdo->prepare("INSERT INTO departments (branch_id, name) VALUES (:branch_id, :name)");
                $stmt->execute([
                    'branch_id' => $data['branch_id'],
                    'name'      => $data['name']
                ]);
                return ['success' => true, 'message' => 'Department created successfully.'];
            } elseif ($action === 'update') {
                $stmt = $pdo->prepare("UPDATE departments SET branch_id = :branch_id, name = :name WHERE id = :id");
                $stmt->execute([
                    'id'        => $data['id'],
                    'branch_id' => $data['branch_id'],
                    'name'      => $data['name']
                ]);
                return ['success' => true, 'message' => 'Department updated successfully.'];
            } elseif ($action === 'delete') {
                $stmt = $pdo->prepare("DELETE FROM departments WHERE id = :id");
                $stmt->execute(['id' => $data['id']]);
                return ['success' => true, 'message' => 'Department deleted successfully.'];
            }
        } catch (\PDOException $e) {
            if ($e->getCode() === '23000' || (isset($e->errorInfo[1]) && $e->errorInfo[1] === 1451)) {
                return ['success' => false, 'message' => 'Cannot delete this record because it is currently linked to one or more active employees. Please reassign the employees first.'];
            }
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
        return ['success' => false, 'message' => 'Invalid action'];
    }

    /**
     * Manage Designation CRUD
     */
    public function manageDesignation(string $action, array $data): array {
        $pdo = get_db_connection();
        try {
            if ($action === 'create') {
                $stmt = $pdo->prepare("INSERT INTO designations (department_id, title) VALUES (:department_id, :title)");
                $stmt->execute([
                    'department_id' => $data['department_id'],
                    'title'         => $data['title']
                ]);
                return ['success' => true, 'message' => 'Designation created successfully.'];
            } elseif ($action === 'update') {
                $stmt = $pdo->prepare("UPDATE designations SET department_id = :department_id, title = :title WHERE id = :id");
                $stmt->execute([
                    'id'            => $data['id'],
                    'department_id' => $data['department_id'],
                    'title'         => $data['title']
                ]);
                return ['success' => true, 'message' => 'Designation updated successfully.'];
            } elseif ($action === 'delete') {
                $stmt = $pdo->prepare("DELETE FROM designations WHERE id = :id");
                $stmt->execute(['id' => $data['id']]);
                return ['success' => true, 'message' => 'Designation deleted successfully.'];
            }
        } catch (\PDOException $e) {
            if ($e->getCode() === '23000' || (isset($e->errorInfo[1]) && $e->errorInfo[1] === 1451)) {
                return ['success' => false, 'message' => 'Cannot delete this record because it is currently linked to one or more active employees. Please reassign the employees first.'];
            }
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
        return ['success' => false, 'message' => 'Invalid action'];
    }

    /**
     * Manage Careers CRUD
     */
    public function manageCareer(string $action, array $data): array {
        $pdo = get_db_connection();
        try {
            if ($action === 'create') {
                $stmt = $pdo->prepare("INSERT INTO careers (title, department_id, branch_id, type, status) VALUES (:title, :department_id, :branch_id, :type, 'Active')");
                $stmt->execute([
                    'title'         => $data['title'],
                    'department_id' => $data['department_id'],
                    'branch_id'     => $data['branch_id'],
                    'type'          => $data['type']
                ]);
                return ['success' => true, 'message' => 'Career opportunity created successfully.'];
            } elseif ($action === 'update') {
                $stmt = $pdo->prepare("UPDATE careers SET title = :title, department_id = :department_id, branch_id = :branch_id, type = :type, status = :status WHERE id = :id");
                $stmt->execute([
                    'id'            => $data['id'],
                    'title'         => $data['title'],
                    'department_id' => $data['department_id'],
                    'branch_id'     => $data['branch_id'],
                    'type'          => $data['type'],
                    'status'        => $data['status'] ?? 'Active'
                ]);
                return ['success' => true, 'message' => 'Career opportunity updated successfully.'];
            } elseif ($action === 'delete') {
                $stmt = $pdo->prepare("DELETE FROM careers WHERE id = :id");
                $stmt->execute(['id' => $data['id']]);
                return ['success' => true, 'message' => 'Career opportunity deleted successfully.'];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
        return ['success' => false, 'message' => 'Invalid action'];
    }
}
