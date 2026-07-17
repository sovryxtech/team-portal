-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 17, 2026 at 05:56 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `verification_logs`;
DROP TABLE IF EXISTS `login_logs`;
DROP TABLE IF EXISTS `activity_logs`;
DROP TABLE IF EXISTS `employee_documents`;
DROP TABLE IF EXISTS `employee_profiles`;
DROP TABLE IF EXISTS `employees`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `roles`;
DROP TABLE IF EXISTS `careers`;
DROP TABLE IF EXISTS `designations`;
DROP TABLE IF EXISTS `departments`;
DROP TABLE IF EXISTS `branches`;
DROP TABLE IF EXISTS `companies`;
DROP TABLE IF EXISTS `contact_messages`;
DROP TABLE IF EXISTS `registration_requests`;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `employee_portal`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `action` varchar(150) NOT NULL,
  `details` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `details`, `timestamp`) VALUES
(1, 1, 'Approved registration request', 'Approved Request ID: 1, Assigned Employee ID: EMP-2026-0002', '2026-07-17 12:26:46'),
(2, 2, 'User login', 'Successfully logged in', '2026-07-17 12:53:45'),
(3, 2, 'User logout', 'Successfully logged out', '2026-07-17 12:55:50'),
(4, 1, 'User login', 'Successfully logged in', '2026-07-17 12:56:37'),
(5, 1, 'User logout', 'Successfully logged out', '2026-07-17 13:01:36'),
(6, NULL, 'Employee registration request submitted', 'Applicant Name: Waibhav Mehta, Email: waibhavm@gmail.com', '2026-07-17 13:20:23'),
(7, 1, 'User login', 'Successfully logged in', '2026-07-17 13:20:47'),
(8, 1, 'User logout', 'Successfully logged out', '2026-07-17 13:22:19'),
(9, 1, 'User login', 'Successfully logged in', '2026-07-17 13:23:31'),
(10, 1, 'Approved registration request', 'Approved Request ID: 2, Assigned Employee ID: EMP-2026-0035', '2026-07-17 13:24:29'),
(11, 1, 'User logout', 'Successfully logged out', '2026-07-17 13:25:04'),
(12, 4, 'User login', 'Successfully logged in', '2026-07-17 13:25:13'),
(13, 4, 'User logout', 'Successfully logged out', '2026-07-17 13:28:30'),
(14, 1, 'User login', 'Successfully logged in', '2026-07-17 13:28:44'),
(15, 1, 'User logout', 'Successfully logged out', '2026-07-17 13:52:30'),
(16, 2, 'User login', 'Successfully logged in', '2026-07-17 14:01:22'),
(17, 2, 'User logout', 'Successfully logged out', '2026-07-17 14:02:16'),
(18, 1, 'User login', 'Successfully logged in', '2026-07-17 14:02:49'),
(19, 1, 'User login', 'Successfully logged in', '2026-07-17 15:10:28'),
(20, 1, 'User logout', 'Successfully logged out', '2026-07-17 15:19:53'),
(21, 1, 'User login', 'Successfully logged in', '2026-07-17 15:20:27'),
(22, 1, 'User logout', 'Successfully logged out', '2026-07-17 15:25:11'),
(23, 2, 'User login', 'Successfully logged in', '2026-07-17 15:25:25'),
(24, 2, 'Updated profile contact info', 'Employee ID: 1', '2026-07-17 15:26:06'),
(25, 2, 'User logout', 'Successfully logged out', '2026-07-17 15:27:14'),
(26, 2, 'User login', 'Successfully logged in', '2026-07-17 15:27:31'),
(27, NULL, 'Contact form submitted', 'Sender: Mohit Shrestha, Subject: Thank You for Contacting Me %from%', '2026-07-17 15:29:42'),
(28, 2, 'User logout', 'Successfully logged out', '2026-07-17 15:29:48'),
(29, 1, 'User login', 'Successfully logged in', '2026-07-17 15:30:07'),
(30, 1, 'User logout', 'Successfully logged out', '2026-07-17 15:36:49'),
(31, 1, 'User login', 'Successfully logged in', '2026-07-17 15:37:05');

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` int(10) UNSIGNED NOT NULL,
  `company_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `contact` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `company_id`, `name`, `address`, `contact`, `created_at`) VALUES
(1, 1, 'Biratnagar', 'Biratnagar, Nepal', '+977 974-4907976', '2026-07-17 12:21:20');

-- --------------------------------------------------------

--
-- Table structure for table `careers`
--

CREATE TABLE `careers` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(150) NOT NULL,
  `department_id` int(10) UNSIGNED NOT NULL,
  `branch_id` int(10) UNSIGNED NOT NULL,
  `type` enum('Full-time','Part-time','Contract','Intern') DEFAULT 'Full-time',
  `status` enum('Active','Closed') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `contact` varchar(50) DEFAULT NULL,
  `email_settings` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `name`, `logo`, `address`, `contact`, `email_settings`, `created_at`) VALUES
(1, 'Sovryx Tech', '/assets/images/logo.png', 'Biratnagar,Nepal', '+977 974-4907976', '[]', '2026-07-17 12:21:20');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(10) UNSIGNED NOT NULL,
  `branch_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `branch_id`, `name`, `created_at`) VALUES
(1, 1, 'Web Developer', '2026-07-17 12:21:20'),
(2, 1, 'IOT', '2026-07-17 12:21:20'),
(3, 1, 'Content Creation', '2026-07-17 12:21:20');

-- --------------------------------------------------------

--
-- Table structure for table `designations`
--

CREATE TABLE `designations` (
  `id` int(10) UNSIGNED NOT NULL,
  `department_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `designations`
--

INSERT INTO `designations` (`id`, `department_id`, `title`, `created_at`) VALUES
(1, 1, 'Senior Web Developer', '2026-07-17 12:21:20'),
(2, 1, 'Associate Software Engineer', '2026-07-17 12:21:20'),
(3, 2, 'HR Operations Specialist', '2026-07-17 12:21:20'),
(4, 3, 'UI/UX Designer', '2026-07-17 12:21:20');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `employee_custom_id` varchar(50) NOT NULL,
  `company_id` int(10) UNSIGNED NOT NULL,
  `branch_id` int(10) UNSIGNED NOT NULL,
  `department_id` int(10) UNSIGNED NOT NULL,
  `designation_id` int(10) UNSIGNED NOT NULL,
  `employment_type` enum('Full-time','Part-time','Contract','Intern') DEFAULT 'Full-time',
  `joining_date` date NOT NULL,
  `employment_status` enum('Active','Suspended','Terminated','Resigned') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `user_id`, `employee_custom_id`, `company_id`, `branch_id`, `department_id`, `designation_id`, `employment_type`, `joining_date`, `employment_status`) VALUES
(1, 2, 'EMP-2026-0001', 1, 1, 1, 1, 'Full-time', '2026-01-10', 'Active'),
(2, 3, 'EMP-2026-0002', 1, 1, 1, 1, 'Full-time', '2026-07-17', 'Active'),
(3, 4, 'EMP-2026-0035', 1, 1, 1, 1, 'Full-time', '2026-07-17', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `employee_documents`
--

CREATE TABLE `employee_documents` (
  `id` int(10) UNSIGNED NOT NULL,
  `employee_id` int(10) UNSIGNED NOT NULL,
  `document_type` varchar(100) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `status` enum('Pending','Verified','Rejected') DEFAULT 'Pending',
  `verified_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employee_documents`
--

INSERT INTO `employee_documents` (`id`, `employee_id`, `document_type`, `file_path`, `status`, `verified_at`) VALUES
(1, 2, 'Citizenship', 'uploads/documents/test_cit.pdf', 'Verified', '2026-07-17 12:26:46'),
(2, 2, 'Cv', 'uploads/documents/test_cv.pdf', 'Verified', '2026-07-17 12:26:46'),
(3, 2, 'Certificates', 'uploads/documents/test_cert.pdf', 'Verified', '2026-07-17 12:26:46'),
(4, 2, 'Police Clearance', 'uploads/documents/test_police.pdf', 'Verified', '2026-07-17 12:26:46'),
(5, 3, 'Citizenship', 'uploads/documents/documents_6a5a2c177b97b0.72037094_ecb66733e8c8eb96.pdf', 'Verified', '2026-07-17 13:24:29'),
(6, 3, 'Cv', 'uploads/documents/documents_6a5a2c177c8268.08797514_adecd57ed08bb7f8.pdf', 'Verified', '2026-07-17 13:24:29'),
(7, 3, 'Certificates', 'uploads/documents/documents_6a5a2c177cc589.01053207_1a21e664ccb56be1.pdf', 'Verified', '2026-07-17 13:24:29'),
(8, 3, 'Police Clearance', 'uploads/documents/documents_6a5a2c177ce885.51913229_b3575284bfa953e9.pdf', 'Verified', '2026-07-17 13:24:29');

-- --------------------------------------------------------

--
-- Table structure for table `employee_profiles`
--

CREATE TABLE `employee_profiles` (
  `id` int(10) UNSIGNED NOT NULL,
  `employee_id` int(10) UNSIGNED NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `blood_group` varchar(10) DEFAULT NULL,
  `nationality` varchar(100) DEFAULT NULL,
  `marital_status` enum('Single','Married','Divorced','Widowed') DEFAULT 'Single',
  `phone` varchar(30) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `emergency_contact` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employee_profiles`
--

INSERT INTO `employee_profiles` (`id`, `employee_id`, `full_name`, `profile_photo`, `dob`, `gender`, `blood_group`, `nationality`, `marital_status`, `phone`, `address`, `emergency_contact`) VALUES
(1, 1, 'John Doe', NULL, '1995-05-15', 'Male', 'O+', 'Nepali', 'Single', '+977-9841234567', 'Bargachi', '{\"name\":\"Jane Doe\",\"relation\":\"Sister\",\"phone\":\"+977-9841234568\"}'),
(2, 2, 'Alice Tester', 'uploads/profiles/test_photo.jpg', '1998-08-20', 'Female', 'A-', 'Nepali', 'Single', '+977-9851122334', 'Patan, Lalitpur', '{\"name\":\"Bob Tester\",\"relation\":\"Father\",\"phone\":\"+977-9851122335\"}'),
(3, 3, 'Waibhav Mehta', 'uploads/profiles/profiles_6a5a2c177a0f88.40969187_75a10eb6bb8416e3.png', '2065-09-24', 'Male', 'O+', 'Nepali', 'Single', '+9779768811191', 'Inurawa,Sunsari', '{\"name\":\"Suresh Mehta\",\"relation\":\"Father\",\"phone\":\"9842434993\"}');

-- --------------------------------------------------------

--
-- Table structure for table `login_logs`
--

CREATE TABLE `login_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `login_logs`
--

INSERT INTO `login_logs` (`id`, `user_id`, `ip_address`, `user_agent`, `timestamp`) VALUES
(1, 2, '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-17 12:53:45'),
(2, 1, '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-17 12:56:37'),
(3, 1, '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-17 13:20:47'),
(4, 1, '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-17 13:23:31'),
(5, 4, '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-17 13:25:13'),
(6, 1, '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-17 13:28:44'),
(7, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', '2026-07-17 14:01:22'),
(8, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', '2026-07-17 14:02:49'),
(9, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', '2026-07-17 15:10:28'),
(10, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', '2026-07-17 15:20:27'),
(11, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', '2026-07-17 15:25:25'),
(12, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', '2026-07-17 15:27:31'),
(13, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', '2026-07-17 15:30:07'),
(14, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', '2026-07-17 15:37:05');

-- --------------------------------------------------------

--
-- Table structure for table `registration_requests`
--

CREATE TABLE `registration_requests` (
  `id` int(10) UNSIGNED NOT NULL,
  `form_data_json` text NOT NULL,
  `file_paths_json` text NOT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `rejection_reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `registration_requests`
--

INSERT INTO `registration_requests` (`id`, `form_data_json`, `file_paths_json`, `status`, `rejection_reason`, `created_at`) VALUES
(1, '{\"full_name\":\"Alice Tester\",\"dob\":\"1998-08-20\",\"gender\":\"Female\",\"blood_group\":\"A-\",\"nationality\":\"Nepali\",\"phone\":\"+977-9851122334\",\"email\":\"alice@test.com\",\"address\":\"Patan, Lalitpur\",\"emergency_name\":\"Bob Tester\",\"emergency_relation\":\"Father\",\"emergency_phone\":\"+977-9851122335\",\"username\":\"alice.tester\",\"password_hash\":\"$2y$10$f5rw8Dn2G07H1otFkLOw3.zUW6BADgNg8fNv7XVh9xltNVQSTxGSu\"}', '{\"profile_photo\":\"uploads\\/profiles\\/test_photo.jpg\",\"citizenship\":\"uploads\\/documents\\/test_cit.pdf\",\"cv\":\"uploads\\/documents\\/test_cv.pdf\",\"certificates\":\"uploads\\/documents\\/test_cert.pdf\",\"police_clearance\":\"uploads\\/documents\\/test_police.pdf\"}', 'Approved', NULL, '2026-07-17 12:26:46'),
(2, '{\"csrf_token\":\"32432658fef680a61ee01056dbf78b58dd58b06d78d9b2defe6cdd5e4097bef7\",\"full_name\":\"Waibhav Mehta\",\"dob\":\"2065-09-24\",\"gender\":\"Male\",\"blood_group\":\"O+\",\"nationality\":\"Nepali\",\"marital_status\":\"Single\",\"phone\":\"+9779768811191\",\"email\":\"waibhavm@gmail.com\",\"address\":\"Inurawa,Sunsari\",\"emergency_name\":\"Suresh Mehta\",\"emergency_relation\":\"Father\",\"emergency_phone\":\"9842434993\",\"highest_degree\":\"\",\"institution\":\"\",\"experience_summary\":\"\",\"username\":\"waibhav_mehta\",\"password_hash\":\"$2y$10$\\/2iPg7yOXJaGSgMGz9HXNeg9ay8Iv7bzKyhrwR2WM.lpGEkDHMr8C\"}', '{\"profile_photo\":\"uploads\\/profiles\\/profiles_6a5a2c177a0f88.40969187_75a10eb6bb8416e3.png\",\"citizenship\":\"uploads\\/documents\\/documents_6a5a2c177b97b0.72037094_ecb66733e8c8eb96.pdf\",\"cv\":\"uploads\\/documents\\/documents_6a5a2c177c8268.08797514_adecd57ed08bb7f8.pdf\",\"certificates\":\"uploads\\/documents\\/documents_6a5a2c177cc589.01053207_1a21e664ccb56be1.pdf\",\"police_clearance\":\"uploads\\/documents\\/documents_6a5a2c177ce885.51913229_b3575284bfa953e9.pdf\"}', 'Approved', NULL, '2026-07-17 13:20:23');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `permissions` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `permissions`) VALUES
(1, 'Super Admin', '{\"all\":true}'),
(2, 'Admin/HR', '{\"approve_registration\":true,\"manage_employees\":true,\"manage_organization\":true,\"view_logs\":true}'),
(3, 'Employee', '{\"view_profile\":true,\"upload_documents\":true,\"download_id\":true}');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role_id`, `username`, `password_hash`, `email`, `status`, `last_login`, `created_at`) VALUES
(1, 1, 'admin', '$2y$10$LehHNoi5Gxw5T/Vx3YX7DubDDnUVHZHgDUlVV6ISJoBvtMhTamcJ2', 'admin@sovryxtech.com.np', 'Active', '2026-07-17 15:37:05', '2026-07-17 12:21:20'),
(2, 3, 'john.doe', '$2y$10$pmIwfw18hJuNRlRv/n.Mm.ar0SpW8SsiNNIaXuAz3o/l4RATDNUci', 'john.doe@sovryxtech.com.np', 'Active', '2026-07-17 15:27:31', '2026-07-17 12:21:20'),
(3, 3, 'alice.tester', '$2y$10$f5rw8Dn2G07H1otFkLOw3.zUW6BADgNg8fNv7XVh9xltNVQSTxGSu', 'alice@test.com', 'Active', NULL, '2026-07-17 12:26:46'),
(4, 3, 'waibhav_mehta', '$2y$10$/2iPg7yOXJaGSgMGz9HXNeg9ay8Iv7bzKyhrwR2WM.lpGEkDHMr8C', 'waibhavm@gmail.com', 'Active', '2026-07-17 13:25:13', '2026-07-17 13:24:29');

-- --------------------------------------------------------

--
-- Table structure for table `verification_logs`
--

CREATE TABLE `verification_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `employee_id` int(10) UNSIGNED NOT NULL,
  `scanned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `verification_logs`
--

INSERT INTO `verification_logs` (`id`, `employee_id`, `scanned_at`, `ip_address`, `user_agent`) VALUES
(1, 1, '2026-07-17 12:55:06', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36'),
(2, 3, '2026-07-17 13:26:43', '::1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36'),
(3, 1, '2026-07-17 15:27:39', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_activity_logs_user` (`user_id`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_branches_company` (`company_id`);

--
-- Indexes for table `careers`
--
ALTER TABLE `careers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_careers_department` (`department_id`),
  ADD KEY `idx_careers_branch` (`branch_id`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_departments_branch` (`branch_id`);

--
-- Indexes for table `designations`
--
ALTER TABLE `designations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_designations_department` (`department_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `employee_custom_id` (`employee_custom_id`),
  ADD KEY `idx_employees_custom_id` (`employee_custom_id`),
  ADD KEY `idx_employees_company` (`company_id`),
  ADD KEY `idx_employees_branch` (`branch_id`),
  ADD KEY `idx_employees_department` (`department_id`),
  ADD KEY `idx_employees_designation` (`designation_id`);

--
-- Indexes for table `employee_documents`
--
ALTER TABLE `employee_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_documents_employee` (`employee_id`);

--
-- Indexes for table `employee_profiles`
--
ALTER TABLE `employee_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_id` (`employee_id`);

--
-- Indexes for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_login_logs_user` (`user_id`);

--
-- Indexes for table `registration_requests`
--
ALTER TABLE `registration_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_role` (`role_id`),
  ADD KEY `idx_users_username` (`username`),
  ADD KEY `idx_users_email` (`email`);

--
-- Indexes for table `verification_logs`
--
ALTER TABLE `verification_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_verif_logs_employee` (`employee_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `careers`
--
ALTER TABLE `careers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `designations`
--
ALTER TABLE `designations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `employee_documents`
--
ALTER TABLE `employee_documents`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `employee_profiles`
--
ALTER TABLE `employee_profiles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `login_logs`
--
ALTER TABLE `login_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `registration_requests`
--
ALTER TABLE `registration_requests`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `verification_logs`
--
ALTER TABLE `verification_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `fk_activity_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `branches`
--
ALTER TABLE `branches`
  ADD CONSTRAINT `fk_branches_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `careers`
--
ALTER TABLE `careers`
  ADD CONSTRAINT `fk_careers_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_careers_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `departments`
--
ALTER TABLE `departments`
  ADD CONSTRAINT `fk_departments_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `designations`
--
ALTER TABLE `designations`
  ADD CONSTRAINT `fk_designations_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `fk_employees_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_employees_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_employees_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_employees_designation` FOREIGN KEY (`designation_id`) REFERENCES `designations` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_employees_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `employee_documents`
--
ALTER TABLE `employee_documents`
  ADD CONSTRAINT `fk_documents_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `employee_profiles`
--
ALTER TABLE `employee_profiles`
  ADD CONSTRAINT `fk_profiles_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD CONSTRAINT `fk_login_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `verification_logs`
--
ALTER TABLE `verification_logs`
  ADD CONSTRAINT `fk_verification_logs_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
SET FOREIGN_KEY_CHECKS = 1;
