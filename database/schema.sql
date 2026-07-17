-- Database Schema for Employee Management & Verification System
-- Compatible with MySQL 8.0+

CREATE DATABASE IF NOT EXISTS `employee_portal` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `employee_portal`;

-- Disable Foreign Key Checks temporarily to recreate tables cleanly if needed
SET FOREIGN_KEY_CHECKS = 0;

-- 1. companies table
DROP TABLE IF EXISTS `companies`;
CREATE TABLE `companies` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `logo` VARCHAR(255) DEFAULT NULL,
  `address` VARCHAR(255) DEFAULT NULL,
  `contact` VARCHAR(50) DEFAULT NULL,
  `email_settings` TEXT DEFAULT NULL, -- JSON string for SMTP/mailer configuration
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. branches table
DROP TABLE IF EXISTS `branches`;
CREATE TABLE `branches` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `company_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `address` VARCHAR(255) DEFAULT NULL,
  `contact` VARCHAR(50) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_branches_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  INDEX `idx_branches_company` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. departments table
DROP TABLE IF EXISTS `departments`;
CREATE TABLE `departments` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `branch_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_departments_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  INDEX `idx_departments_branch` (`branch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. designations table
DROP TABLE IF EXISTS `designations`;
CREATE TABLE `designations` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `department_id` INT UNSIGNED NOT NULL,
  `title` VARCHAR(100) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_designations_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  INDEX `idx_designations_department` (`department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. roles table
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL UNIQUE,
  `permissions` TEXT DEFAULT NULL -- JSON string for RBAC permissions
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. users table
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `role_id` INT UNSIGNED NOT NULL,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `status` ENUM('Active', 'Inactive') DEFAULT 'Active',
  `last_login` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  INDEX `idx_users_role` (`role_id`),
  INDEX `idx_users_username` (`username`),
  INDEX `idx_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. employees table
DROP TABLE IF EXISTS `employees`;
CREATE TABLE `employees` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL UNIQUE,
  `employee_custom_id` VARCHAR(50) NOT NULL UNIQUE,
  `company_id` INT UNSIGNED NOT NULL,
  `branch_id` INT UNSIGNED NOT NULL,
  `department_id` INT UNSIGNED NOT NULL,
  `designation_id` INT UNSIGNED NOT NULL,
  `employment_type` ENUM('Full-time', 'Part-time', 'Contract', 'Intern') DEFAULT 'Full-time',
  `joining_date` DATE NOT NULL,
  `employment_status` ENUM('Active', 'Suspended', 'Terminated', 'Resigned') DEFAULT 'Active',
  CONSTRAINT `fk_employees_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_employees_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_employees_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_employees_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_employees_designation` FOREIGN KEY (`designation_id`) REFERENCES `designations` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  INDEX `idx_employees_custom_id` (`employee_custom_id`),
  INDEX `idx_employees_company` (`company_id`),
  INDEX `idx_employees_branch` (`branch_id`),
  INDEX `idx_employees_department` (`department_id`),
  INDEX `idx_employees_designation` (`designation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. employee_profiles table
DROP TABLE IF EXISTS `employee_profiles`;
CREATE TABLE `employee_profiles` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `employee_id` INT UNSIGNED NOT NULL UNIQUE,
  `full_name` VARCHAR(150) NOT NULL,
  `profile_photo` VARCHAR(255) DEFAULT NULL,
  `dob` DATE DEFAULT NULL,
  `gender` ENUM('Male', 'Female', 'Other') DEFAULT NULL,
  `blood_group` VARCHAR(10) DEFAULT NULL,
  `nationality` VARCHAR(100) DEFAULT NULL,
  `marital_status` ENUM('Single', 'Married', 'Divorced', 'Widowed') DEFAULT 'Single',
  `phone` VARCHAR(30) DEFAULT NULL,
  `address` VARCHAR(255) DEFAULT NULL,
  `emergency_contact` TEXT DEFAULT NULL, -- JSON string storing emergency contact name, relation, and phone
  CONSTRAINT `fk_profiles_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. employee_documents table
DROP TABLE IF EXISTS `employee_documents`;
CREATE TABLE `employee_documents` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `employee_id` INT UNSIGNED NOT NULL,
  `document_type` VARCHAR(100) NOT NULL, -- e.g. Citizenship, CV, Academic, Police Clearance
  `file_path` VARCHAR(255) NOT NULL,
  `status` ENUM('Pending', 'Verified', 'Rejected') DEFAULT 'Pending',
  `verified_at` TIMESTAMP NULL DEFAULT NULL,
  CONSTRAINT `fk_documents_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  INDEX `idx_documents_employee` (`employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. registration_requests table
DROP TABLE IF EXISTS `registration_requests`;
CREATE TABLE `registration_requests` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `form_data_json` TEXT NOT NULL, -- JSON containing all registration details
  `file_paths_json` TEXT NOT NULL, -- JSON containing paths of uploaded documents
  `status` ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
  `rejection_reason` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. verification_logs table
DROP TABLE IF EXISTS `verification_logs`;
CREATE TABLE `verification_logs` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `employee_id` INT UNSIGNED NOT NULL,
  `scanned_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` VARCHAR(255) DEFAULT NULL,
  CONSTRAINT `fk_verification_logs_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  INDEX `idx_verif_logs_employee` (`employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. login_logs table
DROP TABLE IF EXISTS `login_logs`;
CREATE TABLE `login_logs` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` VARCHAR(255) DEFAULT NULL,
  `timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_login_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  INDEX `idx_login_logs_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. activity_logs table
DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE `activity_logs` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED DEFAULT NULL, -- Nullable to allow logging visitor or system actions
  `action` VARCHAR(150) NOT NULL,
  `details` TEXT DEFAULT NULL,
  `timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_activity_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  INDEX `idx_activity_logs_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
