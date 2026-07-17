<?php
declare(strict_types=1);

$pageTitle = "System Audit Tracker & Logs";
require_once __DIR__ . '/../../includes/dashboard_header.php';
require_once __DIR__ . '/../../includes/utils.php';

$pdo = get_db_connection();

// Cleanup old logs (older than 30 days)
$pdo->exec("DELETE FROM activity_logs WHERE timestamp < NOW() - INTERVAL 30 DAY");
$pdo->exec("DELETE FROM login_logs WHERE timestamp < NOW() - INTERVAL 30 DAY");
$pdo->exec("DELETE FROM verification_logs WHERE scanned_at < NOW() - INTERVAL 30 DAY");

// Fetch login logs
$logins = $pdo->query("
    SELECT ll.*, u.username, u.email 
    FROM login_logs ll 
    JOIN users u ON ll.user_id = u.id 
    ORDER BY ll.id DESC 
    LIMIT 200
")->fetchAll();

// Fetch activity logs
$activities = $pdo->query("
    SELECT al.*, u.username 
    FROM activity_logs al 
    LEFT JOIN users u ON al.user_id = u.id 
    ORDER BY al.id DESC 
    LIMIT 200
")->fetchAll();

// Fetch verification logs
$verifications = $pdo->query("
    SELECT vl.*, ep.full_name, e.employee_custom_id 
    FROM verification_logs vl 
    JOIN employees e ON vl.employee_id = e.id 
    JOIN employee_profiles ep ON e.id = ep.employee_id 
    ORDER BY vl.id DESC 
    LIMIT 200
")->fetchAll();
?>

<!-- Tab Selector Navigation -->
<ul class="nav nav-pills mb-4 gap-2" id="logsTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active px-4 py-2 font-weight-bold" id="activity-tab" data-bs-toggle="tab" data-bs-target="#activity-pane" type="button" role="tab"><i class="fa-solid fa-receipt me-2"></i>Activity Logs</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link px-4 py-2 font-weight-bold" id="login-tab" data-bs-toggle="tab" data-bs-target="#login-pane" type="button" role="tab"><i class="fa-solid fa-right-to-bracket me-2"></i>Login Logs</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link px-4 py-2 font-weight-bold" id="verification-tab" data-bs-toggle="tab" data-bs-target="#verification-pane" type="button" role="tab"><i class="fa-solid fa-qrcode me-2"></i>Verification Logs</button>
    </li>
</ul>

<!-- Tab Contents -->
<div class="tab-content" id="logsTabContent">
    <!-- Activity Logs Pane -->
    <div class="tab-pane fade show active" id="activity-pane" role="tabpanel" tabindex="0">
        <div class="card-custom p-4 bg-white">
            <h5 class="text-primary mb-4">User Action Audit Stream</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle table-custom datatable-export">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Action</th>
                            <th>Details</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($activities as $act): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($act['username'] ?? 'System / Guest') ?></strong></td>
                                <td><span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1"><?= htmlspecialchars($act['action']) ?></span></td>
                                <td><span class="text-secondary small"><?= htmlspecialchars($act['details'] ?? '-') ?></span></td>
                                <td><?= date('Y-m-d H:i:s', strtotime($act['timestamp'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Login Logs Pane -->
    <div class="tab-pane fade" id="login-pane" role="tabpanel" tabindex="0">
        <div class="card-custom p-4 bg-white">
            <h5 class="text-primary mb-4">Login Logs Directory</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle table-custom datatable-export">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Email</th>
                            <th>IP Address</th>
                            <th>User Agent</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logins as $log): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($log['username']) ?></strong></td>
                                <td><?= htmlspecialchars($log['email']) ?></td>
                                <td><code><?= htmlspecialchars($log['ip_address'] ?? '0.0.0.0') ?></code></td>
                                <td><span class="text-secondary small"><?= htmlspecialchars($log['user_agent'] ?? '-') ?></span></td>
                                <td><?= date('Y-m-d H:i:s', strtotime($log['timestamp'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Verification Logs Pane -->
    <div class="tab-pane fade" id="verification-pane" role="tabpanel" tabindex="0">
        <div class="card-custom p-4 bg-white">
            <h5 class="text-primary mb-4">Badge QR Scans Audit</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle table-custom datatable-export">
                    <thead>
                        <tr>
                            <th>Employee ID</th>
                            <th>Employee Name</th>
                            <th>IP Address</th>
                            <th>User Agent</th>
                            <th>Scanned At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($verifications as $v): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($v['employee_custom_id']) ?></strong></td>
                                <td><?= htmlspecialchars($v['full_name']) ?></td>
                                <td><code><?= htmlspecialchars($v['ip_address'] ?? '0.0.0.0') ?></code></td>
                                <td><span class="text-secondary small"><?= htmlspecialchars($v['user_agent'] ?? '-') ?></span></td>
                                <td><?= date('Y-m-d H:i:s', strtotime($v['scanned_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/dashboard_footer.php'; ?>
