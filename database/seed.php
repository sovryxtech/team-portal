<?php
declare(strict_types=1);

/**
 * Database Seeder for Employee Management & Verification System
 */

$host = '127.0.0.1';
$user = 'root';
$pass = '';
$dbName = 'employee_portal';

try {
    // 1. Connect without DB selected to run schema.sql
    echo "Connecting to MySQL server at {$host}...\n";
    $pdo = new PDO("mysql:host={$host}", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    echo "Creating database if not exists...\n";
    $pdo->exec("DROP DATABASE IF EXISTS `{$dbName}`");
    $pdo->exec("CREATE DATABASE `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `{$dbName}`");

    // 2. Read schema.sql and execute
    $schemaFile = __DIR__ . '/schema.sql';
    if (!file_exists($schemaFile)) {
        throw new Exception("schema.sql not found at: {$schemaFile}");
    }

    echo "Executing schema.sql...\n";
    $sql = file_get_contents($schemaFile);
    
    // Execute the database creation and table setup
    $pdo->exec($sql);
    echo "Database and schema created successfully.\n";

    // 3. Connect to the newly created database
    $pdo = new PDO("mysql:host={$host};dbname={$dbName};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // 4. Seed Roles
    echo "Seeding roles...\n";
    $roles = [
        ['id' => 1, 'name' => 'Super Admin', 'permissions' => json_encode(['all' => true])],
        ['id' => 2, 'name' => 'Admin/HR', 'permissions' => json_encode([
            'approve_registration' => true,
            'manage_employees' => true,
            'manage_organization' => true,
            'view_logs' => true
        ])],
        ['id' => 3, 'name' => 'Employee', 'permissions' => json_encode([
            'view_profile' => true,
            'upload_documents' => true,
            'download_id' => true
        ])]
    ];

    $roleStmt = $pdo->prepare("INSERT INTO roles (id, name, permissions) VALUES (:id, :name, :permissions) ON DUPLICATE KEY UPDATE name = VALUES(name), permissions = VALUES(permissions)");
    foreach ($roles as $role) {
        $roleStmt->execute($role);
    }

    // 5. Seed Default Company
    echo "Seeding default company...\n";
    $compStmt = $pdo->prepare("INSERT INTO companies (id, name, logo, address, contact, email_settings) VALUES (:id, :name, :logo, :address, :contact, :email_settings) ON DUPLICATE KEY UPDATE name = VALUES(name), logo = VALUES(logo), address = VALUES(address), contact = VALUES(contact)");
    $compStmt->execute([
        'id' => 1,
        'name' => 'Sovryx Tech',
        'logo' => '/assets/images/logo.png',
        'address' => 'Kathmandu, Nepal',
        'contact' => '+977-1-4400000',
        'email_settings' => json_encode([
            'smtp_host' => 'sandbox.smtp.mailtrap.io',
            'smtp_port' => 2525,
            'smtp_user' => 'placeholder_user',
            'smtp_pass' => 'placeholder_pass',
            'smtp_secure' => 'tls',
            'from_email' => 'noreply@sovryxtech.com.np',
            'from_name' => 'Sovryx Tech HR System'
        ])
    ]);

    // 6. Seed Default Branch
    echo "Seeding default branch...\n";
    $branchStmt = $pdo->prepare("INSERT INTO branches (id, company_id, name, address, contact) VALUES (:id, :company_id, :name, :address, :contact) ON DUPLICATE KEY UPDATE name = VALUES(name), address = VALUES(address)");
    $branchStmt->execute([
        'id' => 1,
        'company_id' => 1,
        'name' => 'Kathmandu HQ',
        'address' => 'Kathmandu, Nepal',
        'contact' => '+977-1-4400000'
    ]);

    // 7. Seed Departments
    echo "Seeding departments...\n";
    $deptStmt = $pdo->prepare("INSERT INTO departments (id, branch_id, name) VALUES (:id, :branch_id, :name) ON DUPLICATE KEY UPDATE name = VALUES(name)");
    $departments = [
        ['id' => 1, 'branch_id' => 1, 'name' => 'Engineering'],
        ['id' => 2, 'branch_id' => 1, 'name' => 'Human Resources'],
        ['id' => 3, 'branch_id' => 1, 'name' => 'Design & Creative']
    ];
    foreach ($departments as $dept) {
        $deptStmt->execute($dept);
    }

    // 8. Seed Designations
    echo "Seeding designations...\n";
    $desigStmt = $pdo->prepare("INSERT INTO designations (id, department_id, title) VALUES (:id, :department_id, :title) ON DUPLICATE KEY UPDATE title = VALUES(title)");
    $designations = [
        ['id' => 1, 'department_id' => 1, 'title' => 'Senior Web Developer'],
        ['id' => 2, 'department_id' => 1, 'title' => 'Associate Software Engineer'],
        ['id' => 3, 'department_id' => 2, 'title' => 'HR Operations Specialist'],
        ['id' => 4, 'department_id' => 3, 'title' => 'UI/UX Designer']
    ];
    foreach ($designations as $desig) {
        $desigStmt->execute($desig);
    }

    // 9. Seed Super Admin User
    echo "Seeding admin user...\n";
    $adminPassword = 'AdminPass123!';
    $passwordHash = password_hash($adminPassword, PASSWORD_BCRYPT);
    $userStmt = $pdo->prepare("INSERT INTO users (id, role_id, username, password_hash, email, status) VALUES (:id, :role_id, :username, :password_hash, :email, :status) ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), email = VALUES(email), status = VALUES(status)");
    $userStmt->execute([
        'id' => 1,
        'role_id' => 1, // Super Admin
        'username' => 'admin',
        'password_hash' => $passwordHash,
        'email' => 'admin@sovryxtech.com.np',
        'status' => 'Active'
    ]);

    // Insert an employee row for the admin to complete their profile (optional, but let's make sure admin user has no employee row by default, or let's create a demo employee for testing)
    echo "Seeding test employee user...\n";
    $empPassword = 'EmployeePass123!';
    $empHash = password_hash($empPassword, PASSWORD_BCRYPT);
    $userStmt->execute([
        'id' => 2,
        'role_id' => 3, // Employee
        'username' => 'john.doe',
        'password_hash' => $empHash,
        'email' => 'john.doe@sovryxtech.com.np',
        'status' => 'Active'
    ]);

    $empStmt = $pdo->prepare("INSERT INTO employees (id, user_id, employee_custom_id, company_id, branch_id, department_id, designation_id, employment_type, joining_date, employment_status) VALUES (:id, :user_id, :employee_custom_id, :company_id, :branch_id, :department_id, :designation_id, :employment_type, :joining_date, :employment_status) ON DUPLICATE KEY UPDATE employee_custom_id = VALUES(employee_custom_id)");
    $empStmt->execute([
        'id' => 1,
        'user_id' => 2,
        'employee_custom_id' => 'EMP-2026-0001',
        'company_id' => 1,
        'branch_id' => 1,
        'department_id' => 1,
        'designation_id' => 1,
        'employment_type' => 'Full-time',
        'joining_date' => '2026-01-10',
        'employment_status' => 'Active'
    ]);

    $profileStmt = $pdo->prepare("INSERT INTO employee_profiles (id, employee_id, full_name, profile_photo, dob, gender, blood_group, nationality, marital_status, phone, address, emergency_contact) VALUES (:id, :employee_id, :full_name, :profile_photo, :dob, :gender, :blood_group, :nationality, :marital_status, :phone, :address, :emergency_contact) ON DUPLICATE KEY UPDATE full_name = VALUES(full_name)");
    $profileStmt->execute([
        'id' => 1,
        'employee_id' => 1,
        'full_name' => 'John Doe',
        'profile_photo' => null,
        'dob' => '1995-05-15',
        'gender' => 'Male',
        'blood_group' => 'O+',
        'nationality' => 'Nepali',
        'marital_status' => 'Single',
        'phone' => '+977-9841234567',
        'address' => 'Baneshwor, Kathmandu',
        'emergency_contact' => json_encode([
            'name' => 'Jane Doe',
            'relation' => 'Sister',
            'phone' => '+977-9841234568'
        ])
    ]);

    echo "Seeding completed successfully!\n";
    echo "Super Admin credentials: admin / AdminPass123!\n";
    echo "Employee credentials: john.doe / EmployeePass123!\n";

} catch (Exception $e) {
    echo "SEEDING ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
