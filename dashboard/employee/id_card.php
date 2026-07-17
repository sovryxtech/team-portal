<?php
declare(strict_types=1);

$pageTitle = "Digital ID Badge";
require_once __DIR__ . '/../../includes/dashboard_header.php';
require_once __DIR__ . '/../../src/Controller/EmployeeController.php';
require_once __DIR__ . '/../../src/Service/QRCodeGenerator.php';

$currentUser = auth_user();
$empController = new \Src\Controller\EmployeeController();
$details = $empController->getDetailsByUserId((int)$currentUser['id']);

if (!$details) {
    echo '<div class="alert alert-danger">Employee profile not found.</div>';
    require_once __DIR__ . '/../../includes/dashboard_footer.php';
    exit;
}

// Generate QR Code linking directly to the public verify endpoint
$qrGenerator = new \Src\Service\QRCodeGenerator();
$verificationUrl = get_base_url() . '/verify.php?id=' . $details['employee_custom_id'];
$qrDataUri = $qrGenerator->generateDataUri($verificationUrl);
?>

<div class="row justify-content-center">
    <div class="col-lg-6 text-center">
        <h5 class="text-secondary mb-4">Official Corporate Identity Card</h5>

        <!-- Digital ID Layout Box -->
        <div class="id-card-container mb-4" id="printableIdCard">
            <!-- Status Badge -->
            <div class="id-card-status-badge">
                <?= htmlspecialchars($details['employment_status']) ?>
            </div>

            <!-- Header -->
            <div class="id-card-header">
                <h5>SOVRYX<span>TECH</span></h5>
                <span class="text-white-50 small" style="font-size: 0.65rem;">Digital Verification Badge</span>
                
                <!-- Profile Photo -->
                <div class="id-card-photo-wrapper">
                    <?php if (!empty($details['profile_photo'])): ?>
                        <img src="<?= get_base_url() . '/' . $details['profile_photo'] ?>" alt="Employee Photo" class="id-card-photo">
                    <?php else: ?>
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center border border-white border-4 shadow-sm" style="width: 100px; height: 100px; font-size: 2.5rem; font-weight: 600; margin: 0 auto;">
                            <?= strtoupper(substr($details['full_name'], 0, 2)) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Body -->
            <div class="id-card-body">
                <h4><?= htmlspecialchars($details['full_name']) ?></h4>
                <p class="designation"><?= htmlspecialchars($details['designation_title']) ?></p>
                
                <div class="id-card-details">
                    <div class="row">
                        <div class="col-5 text-muted">Employee ID:</div>
                        <div class="col-7"><strong><?= htmlspecialchars($details['employee_custom_id']) ?></strong></div>
                    </div>
                    <div class="row">
                        <div class="col-5 text-muted">Department:</div>
                        <div class="col-7"><?= htmlspecialchars($details['department_name']) ?></div>
                    </div>
                    <div class="row">
                        <div class="col-5 text-muted">Office Branch:</div>
                        <div class="col-7"><?= htmlspecialchars($details['branch_name']) ?></div>
                    </div>
                    <div class="row">
                        <div class="col-5 text-muted">Issue Date:</div>
                        <div class="col-7"><?= htmlspecialchars($details['joining_date']) ?></div>
                    </div>
                </div>
            </div>

            <!-- QR Verification Section -->
            <div class="id-card-qr-section">
                <img src="<?= $qrDataUri ?>" alt="Scan to Verify">
                <div class="text-muted small mt-1" style="font-size: 0.6rem;">Scan badge to verify authenticity</div>
            </div>
        </div>

        <!-- Action CTA Triggers -->
        <div class="d-flex justify-content-center gap-2 mb-5">
            <a href="<?= get_base_url() ?>/api/download_id_card.php?id=<?= $details['id'] ?>" target="_blank" class="btn btn-primary px-4 py-2"><i class="fa-solid fa-file-pdf me-2"></i>Download PDF</a>
            <button onclick="window.print();" class="btn btn-secondary-custom px-4 py-2"><i class="fa-solid fa-print me-2"></i>Print ID Card</button>
        </div>
    </div>
</div>

<!-- Print Styles for ID Card only -->
<style>
@media print {
    body * {
        visibility: hidden;
    }
    #printableIdCard, #printableIdCard * {
        visibility: visible;
    }
    #printableIdCard {
        position: absolute;
        left: 50%;
        top: 20%;
        transform: translate(-50%, -20%);
        box-shadow: none;
        border: 1px solid #000;
    }
}
</style>

<?php require_once __DIR__ . '/../../includes/dashboard_footer.php'; ?>
