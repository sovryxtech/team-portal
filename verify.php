<?php
declare(strict_types=1);
$pageTitle = "Public Employee Verification";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/src/Controller/VerificationController.php';

$customId = isset($_GET['id']) ? trim($_GET['id']) : '';
$employee = null;
$searched = false;

if (!empty($customId)) {
    $controller = new \Src\Controller\VerificationController();
    $employee = $controller->verifyEmployee($customId);
    $searched = true;
}
?>

<section class="py-5 bg-light" style="min-height: 80vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <!-- Search Box Card -->
                <div class="card-modern shadow-lg p-4 p-md-5 text-center mb-4">
                    <h3 class="text-dark fw-bold mb-3" style="font-family: 'Poppins', sans-serif;">Public Employee Verification</h3>
                    <p class="text-muted">Enter official Employee ID or scan the QR code to verify credential status.</p>
                    
                    <form method="GET" action="verify.php" class="mt-4">
                        <div class="input-group">
                            <input type="text" name="id" class="form-control form-control-lg text-center" placeholder="e.g. EMP-2026-0001" value="<?= htmlspecialchars($customId) ?>" required>
                            <button class="btn btn-secondary-custom px-4" type="submit"><i class="fa-solid fa-magnifying-glass me-2"></i>Verify</button>
                        </div>
                    </form>
                </div>

                <!-- Verification Output Card -->
                <?php if ($searched): ?>
                    <?php if ($employee): ?>
                        <div class="card-modern shadow-lg p-4 p-md-5 border border-success-subtle text-center bg-white">
                            <!-- Verified Badge -->
                            <div class="mb-4">
                                <div class="verified-stamp">
                                    <i class="fa-solid fa-circle-check me-2"></i>Verified Status
                                </div>
                            </div>

                            <!-- Employee Photo -->
                            <div class="mb-3">
                                <?php if (!empty($employee['profile_photo'])): ?>
                                    <img src="<?= get_base_url() . '/' . $employee['profile_photo'] ?>" alt="Profile Photo" class="rounded-circle border border-primary border-3 shadow-sm" style="width: 130px; height: 130px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center border border-primary border-3 shadow-sm" style="width: 130px; height: 130px; font-size: 3rem; font-weight: 600;">
                                        <?= strtoupper(substr($employee['full_name'], 0, 2)) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Employee Profile Data -->
                            <h4 class="text-primary mb-1"><?= htmlspecialchars($employee['full_name']) ?></h4>
                            <p class="text-secondary font-weight-bold mb-4" style="color: var(--secondary-color) !important;"><?= htmlspecialchars($employee['designation_title']) ?></p>

                            <div class="table-responsive text-start">
                                <table class="table table-sm table-borderless align-middle mb-0">
                                    <tbody>
                                        <tr>
                                            <td class="text-muted w-40">Employee ID:</td>
                                            <td class="font-weight-bold"><strong><?= htmlspecialchars($employee['employee_custom_id']) ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Company Name:</td>
                                            <td><strong><?= htmlspecialchars($employee['company_name']) ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Department:</td>
                                            <td><?= htmlspecialchars($employee['department_name']) ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Branch Location:</td>
                                            <td><?= htmlspecialchars($employee['branch_name']) ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Joining Date:</td>
                                            <td><?= htmlspecialchars($employee['joining_date']) ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Employment Status:</td>
                                            <td>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">
                                                    <?= htmlspecialchars($employee['employment_status']) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Error / Not Verified Case -->
                        <div class="card-modern shadow-lg p-5 border border-danger-subtle text-center bg-white">
                            <div class="text-danger mb-4">
                                <i class="fa-solid fa-triangle-exclamation" style="font-size: 5rem;"></i>
                            </div>
                            <h3 class="text-danger mb-2">NOT VERIFIED</h3>
                            <h5 class="text-muted mb-3">Employee Record Not Found</h5>
                            <p class="text-secondary small mb-0">
                                The employee credentials keyed in (ID: <strong><?= htmlspecialchars($customId) ?></strong>) do not exist or are inactive in the database directory.
                            </p>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
