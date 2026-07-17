<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

// Enforce login
auth_enforce();

$currentUser = auth_user();

if (auth_has_role(['Super Admin', 'Admin/HR'])) {
    header("Location: " . get_base_url() . "/dashboard/admin/index.php");
} else {
    header("Location: " . get_base_url() . "/dashboard/employee/index.php");
}
exit;
