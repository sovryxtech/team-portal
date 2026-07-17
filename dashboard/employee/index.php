<?php
declare(strict_types=1);

$pageTitle = "My Profile Hub";
require_once __DIR__ . '/../../includes/dashboard_header.php';
require_once __DIR__ . '/../../src/Controller/EmployeeController.php';

$currentUser = auth_user();
$empController = new \Src\Controller\EmployeeController();
$details = $empController->getDetailsByUserId((int)$currentUser['id']);

if (!$details) {
    echo '<div class="alert alert-danger">Employee profile not loaded. Please contact HR.</div>';
    require_once __DIR__ . '/../../includes/dashboard_footer.php';
    exit;
}

$emergency = json_decode($details['emergency_contact'] ?? '{}', true);
?>

<div class="row g-4">
    <!-- Profile Welcome and Info Summary -->
    <div class="col-lg-8">
        <!-- Welcome Widget -->
        <div class="card-custom p-4 bg-gradient-primary text-white mb-4">
            <div class="d-flex align-items-center flex-wrap gap-3">
                <?php if (!empty($details['profile_photo'])): ?>
                    <img src="<?= get_base_url() . '/' . $details['profile_photo'] ?>" alt="Profile Photo" class="rounded-circle border border-3 border-white" style="width: 80px; height: 80px; object-fit: cover;">
                <?php else: ?>
                    <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center border border-3 border-white" style="width: 80px; height: 80px; font-size: 2.2rem; font-weight: 600;">
                        <?= strtoupper(substr($details['full_name'], 0, 2)) ?>
                    </div>
                <?php endif; ?>
                <div>
                    <h3 class="mb-1">Hello, <?= htmlspecialchars($details['full_name']) ?>!</h3>
                    <p class="mb-0 text-white-50"><i class="fa-solid fa-briefcase me-2"></i><?= htmlspecialchars($details['designation_title']) ?> at <strong><?= htmlspecialchars($details['company_name']) ?></strong></p>
                </div>
            </div>
        </div>

        <!-- Detailed Profile Card -->
        <div class="card-custom p-4 bg-white mb-4">
            <h5 class="text-primary mb-4"><i class="fa-solid fa-address-card me-2"></i>Employment Summary</h5>
            <div class="row g-3" style="font-size: 0.9rem;">
                <div class="col-sm-6">
                    <span class="text-muted d-block">Official ID Code:</span>
                    <strong><?= htmlspecialchars($details['employee_custom_id']) ?></strong>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted d-block">Joining Date:</span>
                    <strong><?= htmlspecialchars($details['joining_date']) ?></strong>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted d-block">Department & Branch:</span>
                    <strong><?= htmlspecialchars($details['department_name']) ?></strong> (<?= htmlspecialchars($details['branch_name']) ?>)
                </div>
                <div class="col-sm-6">
                    <span class="text-muted d-block">Employment Type:</span>
                    <strong><?= htmlspecialchars($details['employment_type']) ?></strong>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted d-block">Personal Blood Group:</span>
                    <strong><?= htmlspecialchars($details['blood_group'] ?? 'N/A') ?></strong>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted d-block">Nationality:</span>
                    <strong><?= htmlspecialchars($details['nationality'] ?? 'N/A') ?></strong>
                </div>
            </div>
        </div>

        <!-- Contact details update wizard -->
        <div class="card-custom p-4 bg-white">
            <h5 class="text-primary mb-4"><i class="fa-solid fa-user-pen me-2"></i>Update Contact Information</h5>
            
            <form id="profileUpdateForm" action="<?= get_base_url() ?>/api/employee_actions.php" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="update_contact_info">

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" name="phone" class="form-control" value="<?= htmlspecialchars($details['phone'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Residential Address</label>
                        <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($details['address'] ?? '') ?>" required>
                    </div>

                    <h6 class="mt-4 mb-1 text-secondary">Emergency Contact Config</h6>
                    <div class="col-md-4">
                        <label class="form-label text-muted small">Contact Name</label>
                        <input type="text" name="emergency_name" class="form-control" value="<?= htmlspecialchars($emergency['name'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small">Relationship</label>
                        <input type="text" name="emergency_relation" class="form-control" value="<?= htmlspecialchars($emergency['relation'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small">Emergency Phone</label>
                        <input type="tel" name="emergency_phone" class="form-control" value="<?= htmlspecialchars($emergency['phone'] ?? '') ?>" required>
                    </div>
                    
                    <div class="col-12 mt-4 text-end">
                        <button type="submit" class="btn btn-primary px-4"><i class="fa-solid fa-floppy-disk me-2"></i>Save Changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulletin Announcement Board -->
    <div class="col-lg-4">
        <div class="card-custom p-4 bg-white h-100">
            <h5 class="text-primary mb-4"><i class="fa-solid fa-bullhorn me-2"></i>Company Bulletin Feed</h5>
            
            <div class="d-flex flex-column gap-3">
                <div class="p-3 bg-light rounded-3 border-start border-primary border-4">
                    <div class="d-flex justify-content-between mb-1">
                        <strong>Office Deployment Notice</strong>
                        <span class="text-muted small">Today</span>
                    </div>
                    <p class="text-secondary small mb-0">Portal deployment testing has officially commenced. Please inspect and update profile directories.</p>
                </div>

                <div class="p-3 bg-light rounded-3 border-start border-warning border-4">
                    <div class="d-flex justify-content-between mb-1">
                        <strong>SMTP Verification Logs</strong>
                        <span class="text-muted small">Yesterday</span>
                    </div>
                    <p class="text-secondary small mb-0">Applicants will now receive welcome/rejection mail templates in local email sandbox directories.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    setupAjaxForm('#profileUpdateForm');
});
</script>

<?php require_once __DIR__ . '/../../includes/dashboard_footer.php'; ?>
