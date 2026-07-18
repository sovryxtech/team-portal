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
        <nav class="navbar navbar-expand navbar-light bg-white px-4 py-3 mb-4 rounded-4 shadow-sm" style="border: 1px solid rgba(0,0,0,0.03);">
            <div class="container-fluid p-0">
                <div class="d-flex flex-column">
                    <h4 class="mb-0 font-weight-bold text-primary" style="font-family: 'Poppins', sans-serif;"><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Dashboard' ?></h4>
                    <span class="text-secondary small d-none d-md-inline">Welcome back, <?= htmlspecialchars(explode(' ', trim($currentUser['full_name']))[0]) ?>!</span>
                </div>
                
                <div class="ms-auto d-flex align-items-center gap-4">
                    <!-- Search Bar -->
                    <div class="d-none d-lg-flex position-relative">
                        <i class="fa-solid fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                        <input type="text" class="form-control rounded-pill bg-light border-0 ps-5 pe-4 py-2" placeholder="Search..." style="width: 250px; font-size: 0.9rem;">
                    </div>

                    <!-- Language Switcher -->
                    <div class="dropdown d-none d-sm-block">
                        <a href="#" class="text-secondary text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-globe fs-5"></i> EN
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3" style="border-radius: 12px; min-width: 120px;">
                            <li><a class="dropdown-item active" href="#">English (EN)</a></li>
                            <li><a class="dropdown-item" href="#">Nepali (NE)</a></li>
                            <li><a class="dropdown-item" href="#">Spanish (ES)</a></li>
                        </ul>
                    </div>

                    <!-- Dark Mode Toggle -->
                    <a href="#" class="text-secondary text-decoration-none d-none d-sm-block" id="darkModeToggle" title="Toggle Dark Mode">
                        <i class="fa-solid fa-moon fs-5"></i>
                    </a>

                    <!-- Notifications -->
                    <div class="dropdown">
                        <a href="#" class="text-secondary position-relative text-decoration-none" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-regular fa-bell fs-4"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                                3
                                <span class="visually-hidden">unread messages</span>
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3 p-0" style="width: 320px; border-radius: 12px; overflow: hidden;">
                            <li class="bg-light p-3 border-bottom"><h6 class="mb-0">Notifications</h6></li>
                            <li><a class="dropdown-item py-3 border-bottom" href="#"><i class="fa-solid fa-envelope text-primary me-2"></i> New Announcement posted</a></li>
                            <li><a class="dropdown-item py-3 border-bottom" href="#"><i class="fa-solid fa-calendar text-warning me-2"></i> Upcoming event tomorrow</a></li>
                            <li><a class="dropdown-item py-3 text-center text-primary fw-bold" href="#">View All Notifications</a></li>
                        </ul>
                    </div>

                    <!-- User Profile Dropdown -->
                    <div class="dropdown">
                        <a class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark" href="#" role="button" id="userMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php if (!empty($currentUser['profile_photo'])): ?>
                                <img src="<?= get_base_url() . '/' . $currentUser['profile_photo'] ?>" alt="Profile" class="rounded-circle" style="width: 44px; height: 44px; object-fit: cover; border: 2px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                            <?php else: ?>
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; font-weight: 600; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                    <?= strtoupper(substr($currentUser['username'], 0, 2)) ?>
                                </div>
                            <?php endif; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3" aria-labelledby="userMenuLink" style="border-radius: 12px;">
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
