<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/csrf.php';

// Enforce authentication
auth_enforce();

$currentUser = auth_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Dashboard' ?> | HR System</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome 6 for Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet">
    
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
    <!-- Custom Style Sheet -->
    <link href="<?= get_base_url() ?>/assets/css/style.css" rel="stylesheet">
    
    <!-- Chart.js for Analytics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<div class="dashboard-wrapper">
    <!-- Sidebar included here dynamically -->
    <?php require_once __DIR__ . '/dashboard_sidebar.php'; ?>

    <!-- Main Content Area -->
    <div class="dashboard-content">
        <!-- Top Navbar in Dashboard -->
        <nav class="navbar navbar-expand navbar-light bg-white px-4 py-3 mb-4 rounded-4 shadow-sm">
            <div class="container-fluid p-0">
                <h4 class="mb-0 font-weight-bold text-primary"><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Dashboard' ?></h4>
                <div class="ms-auto d-flex align-items-center">
                    <span class="text-secondary me-3 d-none d-md-inline">Welcome, <strong><?= htmlspecialchars($currentUser['full_name']) ?></strong></span>
                    <div class="dropdown">
                        <a class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark" href="#" role="button" id="userMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php if (!empty($currentUser['profile_photo'])): ?>
                                <img src="<?= get_base_url() . '/' . $currentUser['profile_photo'] ?>" alt="Profile" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover; border: 2px solid var(--secondary-color);">
                            <?php else: ?>
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-weight: 600;">
                                    <?= strtoupper(substr($currentUser['username'], 0, 2)) ?>
                                </div>
                            <?php endif; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="userMenuLink">
                            <li><span class="dropdown-header">Signed in as <strong><?= htmlspecialchars($currentUser['username']) ?></strong></span></li>
                            <li><hr class="dropdown-divider"></li>
                            <?php if ($currentUser['role_name'] === 'Employee'): ?>
                                <li><a class="dropdown-item" href="<?= get_base_url() ?>/dashboard/employee/index.php"><i class="fa-solid fa-user me-2"></i>My Profile</a></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item text-danger" href="<?= get_base_url() ?>/logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
