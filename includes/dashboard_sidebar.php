<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';
$currentUser = auth_user();
$currentFile = basename($_SERVER['PHP_SELF']);
$currentDir = basename(dirname($_SERVER['PHP_SELF']));
?>
<div class="dashboard-sidebar d-flex flex-column">
    <div class="sidebar-header">
        <h4>SOVRYX<span>PORTAL</span></h4>
    </div>
    
    <div class="sidebar-user-card">
        <?php if (!empty($currentUser['profile_photo'])): ?>
            <img src="<?= get_base_url() . '/' . $currentUser['profile_photo'] ?>" alt="Profile Photo">
        <?php else: ?>
            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; font-weight: 600; border: 2px solid var(--secondary-color);">
                <?= strtoupper(substr($currentUser['username'], 0, 2)) ?>
            </div>
        <?php endif; ?>
        <div class="user-info">
            <h6><?= htmlspecialchars($currentUser['full_name']) ?></h6>
            <span><?= htmlspecialchars($currentUser['role_name']) ?></span>
        </div>
    </div>
    
    <ul class="components flex-grow-1">
        <?php if (auth_has_role(['Super Admin', 'Admin/HR'])): ?>
            <!-- Admin Navigation links -->
            <li class="<?= $currentDir === 'admin' && $currentFile === 'index.php' ? 'active' : '' ?>">
                <a href="<?= get_base_url() ?>/dashboard/admin/index.php">
                    <i class="fa-solid fa-chart-pie"></i> Overview
                </a>
            </li>
            <li class="<?= $currentDir === 'admin' && $currentFile === 'registrations.php' ? 'active' : '' ?>">
                <a href="<?= get_base_url() ?>/dashboard/admin/registrations.php">
                    <i class="fa-solid fa-user-plus"></i> Registrations
                </a>
            </li>
            <li class="<?= $currentDir === 'admin' && $currentFile === 'employees.php' ? 'active' : '' ?>">
                <a href="<?= get_base_url() ?>/dashboard/admin/employees.php">
                    <i class="fa-solid fa-users"></i> Employees Directory
                </a>
            </li>
            <li class="<?= $currentDir === 'admin' && $currentFile === 'organization.php' ? 'active' : '' ?>">
                <a href="<?= get_base_url() ?>/dashboard/admin/organization.php">
                    <i class="fa-solid fa-sitemap"></i> Organization CRUD
                </a>
            </li>
            <li class="<?= $currentDir === 'admin' && $currentFile === 'org_chart.php' ? 'active' : '' ?>">
                <a href="<?= get_base_url() ?>/dashboard/admin/org_chart.php">
                    <i class="fa-solid fa-diagram-project"></i> Interactive Org Chart
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="#communicationsSubmenu" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="communicationsSubmenu">
                    <i class="fa-solid fa-bullhorn"></i> Communications <i class="fa-solid fa-caret-down ms-auto"></i>
                </a>
                <ul class="collapse list-unstyled <?= in_array($currentFile, ['templates.php', 'announcements.php', 'news.php', 'events.php']) ? 'show' : '' ?>" id="communicationsSubmenu">
                    <li class="<?= $currentFile === 'templates.php' ? 'active' : '' ?>">
                        <a href="<?= get_base_url() ?>/dashboard/admin/templates.php"><i class="fa-solid fa-envelope"></i> Email Templates</a>
                    </li>
                    <li class="<?= $currentFile === 'announcements.php' ? 'active' : '' ?>">
                        <a href="<?= get_base_url() ?>/dashboard/admin/announcements.php"><i class="fa-solid fa-bell"></i> Announcements</a>
                    </li>
                    <li class="<?= $currentFile === 'news.php' ? 'active' : '' ?>">
                        <a href="<?= get_base_url() ?>/dashboard/admin/news.php"><i class="fa-solid fa-newspaper"></i> Company News</a>
                    </li>
                    <li class="<?= $currentFile === 'events.php' ? 'active' : '' ?>">
                        <a href="<?= get_base_url() ?>/dashboard/admin/events.php"><i class="fa-solid fa-calendar-star"></i> Event Notifications</a>
                    </li>
                </ul>
            </li>

            <li class="<?= $currentDir === 'admin' && $currentFile === 'logs.php' ? 'active' : '' ?>">
                <a href="<?= get_base_url() ?>/dashboard/admin/logs.php">
                    <i class="fa-solid fa-receipt"></i> System Audit Logs
                </a>
            </li>
            <li class="<?= $currentDir === 'admin' && $currentFile === 'messages.php' ? 'active' : '' ?>">
                <a href="<?= get_base_url() ?>/dashboard/admin/messages.php">
                    <i class="fa-solid fa-comments"></i> Support Messages
                </a>
            </li>
        <?php else: ?>
            <!-- Employee Navigation links -->
            <li class="<?= $currentDir === 'employee' && $currentFile === 'index.php' ? 'active' : '' ?>">
                <a href="<?= get_base_url() ?>/dashboard/employee/index.php">
                    <i class="fa-solid fa-user-astronaut"></i> My Profile Hub
                </a>
            </li>
            <li class="<?= $currentDir === 'employee' && $currentFile === 'communications.php' ? 'active' : '' ?>">
                <a href="<?= get_base_url() ?>/dashboard/employee/communications.php">
                    <i class="fa-solid fa-tower-broadcast"></i> Communications
                </a>
            </li>
            <li class="<?= $currentDir === 'employee' && $currentFile === 'id_card.php' ? 'active' : '' ?>">
                <a href="<?= get_base_url() ?>/dashboard/employee/id_card.php">
                    <i class="fa-solid fa-address-card"></i> Digital ID Badge
                </a>
            </li>
            <li class="<?= $currentDir === 'employee' && $currentFile === 'documents.php' ? 'active' : '' ?>">
                <a href="<?= get_base_url() ?>/dashboard/employee/documents.php">
                    <i class="fa-solid fa-file-shield"></i> Document Vault
                </a>
            </li>
        <?php endif; ?>
        
        <hr class="mx-3 my-3 text-white-50">
        
        <!-- Shared navigation links -->
        <li>
            <a href="<?= get_base_url() ?>/index.php">
                <i class="fa-solid fa-house"></i> Public Homepage
            </a>
        </li>
        <li>
            <a href="<?= get_base_url() ?>/verify.php">
                <i class="fa-solid fa-qrcode"></i> Public Verify Tool
            </a>
        </li>
        <li>
            <a href="<?= get_base_url() ?>/logout.php" class="text-danger">
                <i class="fa-solid fa-power-off text-danger"></i> Logout Session
            </a>
        </li>
    </ul>
</div>
