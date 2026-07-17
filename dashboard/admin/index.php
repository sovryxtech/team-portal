<?php
declare(strict_types=1);

$pageTitle = "System Administration Overview";
require_once __DIR__ . '/../../includes/dashboard_header.php';
require_once __DIR__ . '/../../src/Controller/AdminController.php';

$adminController = new \Src\Controller\AdminController();
$metrics = $adminController->getDashboardMetrics();
?>

<!-- Metrics Row -->
<div class="row g-3 mb-4">
    <!-- Total Employees -->
    <div class="col-md-3">
        <div class="metric-card bg-gradient-primary">
            <h5>Total Directory</h5>
            <h2><?= $metrics['total_employees'] ?></h2>
            <p class="mb-0 text-white-50">Registered Employees</p>
            <i class="fa-solid fa-users"></i>
        </div>
    </div>
    
    <!-- Active Employees -->
    <div class="col-md-3">
        <div class="metric-card bg-success">
            <h5>Active Staff</h5>
            <h2><?= $metrics['active_employees'] ?></h2>
            <p class="mb-0 text-white-50">Currently Employed</p>
            <i class="fa-solid fa-user-check"></i>
        </div>
    </div>
    
    <!-- Pending Requests -->
    <div class="col-md-3">
        <div class="metric-card bg-warning">
            <h5>Pending Actions</h5>
            <h2><?= $metrics['pending_registrations'] ?></h2>
            <p class="mb-0 text-white-50">New Applicant Registrations</p>
            <i class="fa-solid fa-user-pen"></i>
        </div>
    </div>
    
    <!-- Scanned Badges -->
    <div class="col-md-3">
        <div class="metric-card bg-info">
            <h5>Verification Scans</h5>
            <h2><?= $metrics['total_scans'] ?></h2>
            <p class="mb-0 text-white-50 font-weight-bold" style="color: var(--primary-color) !important;">Public ID Inquiries</p>
            <i class="fa-solid fa-qrcode"></i>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card-custom p-4 bg-white">
            <h5 class="text-primary mb-3"><i class="fa-solid fa-chart-bar me-2"></i>Employee Distribution by Department</h5>
            <canvas id="deptChart" style="max-height: 250px;"></canvas>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card-custom p-4 bg-white">
            <h5 class="text-primary mb-3"><i class="fa-solid fa-chart-pie me-2"></i>Employee Distribution by Branch</h5>
            <canvas id="branchChart" style="max-height: 250px;"></canvas>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Applications Stream -->
    <div class="col-lg-6">
        <div class="card-custom p-4 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="text-primary mb-0"><i class="fa-solid fa-user-plus me-2"></i>Recent Registration Streams</h5>
                <a href="registrations.php" class="btn btn-primary btn-sm px-3">View All</a>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Applicant</th>
                            <th>Submitted</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($metrics['recent_registrations'])): ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted py-3">No pending registration requests.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($metrics['recent_registrations'] as $reg): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($reg['full_name']) ?></strong><br>
                                        <span class="text-muted small"><?= htmlspecialchars($reg['email']) ?></span>
                                    </td>
                                    <td><?= date('Y-m-d H:i', strtotime($reg['created_at'])) ?></td>
                                    <td class="text-end">
                                        <a href="registrations.php?id=<?= $reg['id'] ?>" class="btn btn-outline-primary btn-sm">Process</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent System Activity Logs -->
    <div class="col-lg-6">
        <div class="card-custom p-4 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="text-primary mb-0"><i class="fa-solid fa-clock-rotate-left me-2"></i>System Activity Tracker</h5>
                <a href="logs.php" class="btn btn-primary btn-sm px-3">View All</a>
            </div>
            
            <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                <ul class="list-group list-group-flush">
                    <?php if (empty($metrics['recent_activities'])): ?>
                        <li class="list-group-item text-center text-muted">No activity logs recorded.</li>
                    <?php else: ?>
                        <?php foreach ($metrics['recent_activities'] as $act): ?>
                            <li class="list-group-item px-0 py-2 border-0 border-bottom">
                                <div class="d-flex justify-content-between">
                                    <strong><?= htmlspecialchars($act['username'] ?? 'System / Guest') ?></strong>
                                    <span class="text-muted small"><?= date('H:i:s', strtotime($act['timestamp'])) ?></span>
                                </div>
                                <div class="small text-secondary"><?= htmlspecialchars($act['action']) ?></div>
                                <?php if (!empty($act['details'])): ?>
                                    <div class="text-muted small" style="font-size: 0.75rem;"><?= htmlspecialchars($act['details']) ?></div>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Chart initialization scripts -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    // 1. Department Chart
    var deptData = <?= json_encode($metrics['department_chart']) ?>;
    var deptLabels = deptData.map(item => item.dept_name);
    var deptCounts = deptData.map(item => item.emp_count);

    new Chart(document.getElementById('deptChart'), {
        type: 'bar',
        data: {
            labels: deptLabels,
            datasets: [{
                label: 'Employees',
                data: deptCounts,
                backgroundColor: 'rgba(11, 37, 73, 0.85)',
                borderColor: '#0B2545',
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });

    // 2. Branch Chart
    var branchData = <?= json_encode($metrics['branch_chart']) ?>;
    var branchLabels = branchData.map(item => item.branch_name);
    var branchCounts = branchData.map(item => item.emp_count);

    new Chart(document.getElementById('branchChart'), {
        type: 'doughnut',
        data: {
            labels: branchLabels,
            datasets: [{
                data: branchCounts,
                backgroundColor: [
                    '#0B2545',
                    '#F79F1F',
                    '#10B981',
                    '#EF4444',
                    '#134074'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
});
</script>

<?php require_once __DIR__ . '/../../includes/dashboard_footer.php'; ?>
