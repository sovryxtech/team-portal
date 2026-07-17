<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/csrf.php';
$currentUser = auth_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' : '' ?>Sovryx Tech Employee Portal</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome 6 for Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
    <!-- Custom Style Sheet -->
    <link href="<?= get_base_url() ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>

    <!-- Public Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="<?= get_base_url() ?>/index.php">
                <i class="fa-solid fa-people-group me-2"></i>SOVRYX<span>TECH</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#publicNavbar" aria-controls="publicNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="publicNavbar">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>" href="<?= get_base_url() ?>/index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : '' ?>" href="<?= get_base_url() ?>/about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'careers.php' ? 'active' : '' ?>" href="<?= get_base_url() ?>/careers.php">Careers</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'verify.php' ? 'active' : '' ?>" href="<?= get_base_url() ?>/verify.php">Verify Employee</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : '' ?>" href="<?= get_base_url() ?>/contact.php">Contact</a>
                    </li>
                    <?php if ($currentUser): ?>
                        <li class="nav-item dropdown">
                            <a class="btn btn-secondary-custom ms-lg-3 px-4" href="<?= get_base_url() ?>/dashboard/index.php">
                                <i class="fa-solid fa-gauge me-2"></i>Dashboard
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="btn btn-secondary-custom ms-lg-3 px-4" href="<?= get_base_url() ?>/login.php">
                                <i class="fa-solid fa-lock-open me-2"></i>Login
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
