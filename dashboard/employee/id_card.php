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
$verificationUrl = get_base_url() . '/verify/index.php?id=' . $details['employee_custom_id'];
$qrDataUri = $qrGenerator->generateDataUri($verificationUrl);
?>

<div class="row justify-content-center">
    <div class="col-lg-6 text-center">
        <h5 class="text-secondary fw-bold mb-4">Official Corporate Identity Card</h5>

        <!-- Digital ID Layout Box -->
        <div class="id-card-modern mb-5 mx-auto" id="printableIdCard">
            
            <!-- Header Section -->
            <div class="id-card-header bg-gradient-primary p-4 position-relative overflow-hidden text-start">
                <div class="position-absolute" style="top: -20px; right: -20px; width: 150px; height: 150px; background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 60%); border-radius: 50%;"></div>
                
                <div class="d-flex justify-content-between align-items-start position-relative z-1">
                    <div>
                        <h4 class="text-white fw-bold mb-0" style="font-family: 'Poppins', sans-serif;">SOVRYX<span class="text-warning">TECH</span></h4>
                        <span class="text-white-50" style="font-size: 0.65rem; letter-spacing: 1px;">EMPLOYEE IDENTITY CARD</span>
                    </div>
                    <span class="badge bg-success rounded-pill px-3 py-1 shadow-sm"><i class="fa-solid fa-circle me-1" style="font-size: 0.5rem;"></i> Active</span>
                </div>
            </div>

            <!-- Body Section -->
            <div class="id-card-body bg-white p-4 position-relative">
                <!-- Floating Profile Photo -->
                <div class="position-absolute start-50 translate-middle-x" style="top: -45px;">
                    <div class="bg-white p-1 rounded-circle shadow">
                        <?php if (!empty($details['profile_photo'])): ?>
                            <img src="<?= get_base_url() . '/' . $details['profile_photo'] ?>" alt="Employee Photo" class="rounded-circle border" style="width: 80px; height: 80px; object-fit: cover;">
                        <?php else: ?>
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center border" style="width: 80px; height: 80px; font-size: 2rem; font-weight: 600;">
                                <?= strtoupper(substr($details['full_name'], 0, 2)) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mt-4 pt-3 text-center">
                    <h4 class="text-dark fw-bold mb-1" style="font-family: 'Poppins', sans-serif;"><?= htmlspecialchars($details['full_name']) ?></h4>
                    <span class="d-block text-primary fw-semibold mb-3" style="font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;"><?= htmlspecialchars($details['designation_title']) ?></span>
                </div>

                <!-- Info Grid -->
                <div class="row g-2 text-start mt-2">
                    <div class="col-6">
                        <div class="p-2 bg-light rounded-3 border">
                            <span class="d-block text-muted" style="font-size: 0.65rem; text-transform: uppercase;">Employee ID</span>
                            <strong class="text-dark" style="font-size: 0.85rem;"><?= htmlspecialchars($details['employee_custom_id']) ?></strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 bg-light rounded-3 border">
                            <span class="d-block text-muted" style="font-size: 0.65rem; text-transform: uppercase;">Department</span>
                            <strong class="text-dark text-truncate d-block" style="font-size: 0.85rem;"><?= htmlspecialchars($details['department_name']) ?></strong>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-2 bg-light rounded-3 border">
                            <span class="d-block text-muted" style="font-size: 0.65rem; text-transform: uppercase;">Branch / Location</span>
                            <strong class="text-dark text-truncate d-block" style="font-size: 0.85rem;"><?= htmlspecialchars($details['branch_name']) ?></strong>
                        </div>
                    </div>
                </div>

                <!-- QR Code & Signature -->
                <div class="d-flex justify-content-between align-items-end mt-4">
                    <div class="text-start">
                        <div class="mb-1" style="font-family: 'Brush Script MT', cursive; font-size: 24px; border-bottom: 1px solid #E2E8F0; width: 120px;">Approved</div>
                        <span class="text-muted fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">AUTHORIZED SIGNATORY</span>
                    </div>
                    <div class="bg-light p-1 rounded-3 border shadow-sm">
                        <img src="<?= $qrDataUri ?>" alt="QR Code" style="width: 70px; height: 70px;">
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="id-card-footer bg-light p-2 border-top text-center text-muted" style="font-size: 0.65rem; letter-spacing: 0.5px;">
                <strong>Sovryx Tech Pvt. Ltd.</strong> • sovryx.tech@gmail.com
            </div>
        </div>

        <!-- Action CTA Triggers -->
        <div class="d-flex justify-content-center gap-3 mb-5">
            <button onclick="window.print();" class="btn btn-primary px-4 py-2 fw-bold shadow-sm rounded-pill"><i class="fa-solid fa-print me-2"></i>Print ID Card</button>
            <a href="#" class="btn btn-light border px-4 py-2 fw-bold shadow-sm rounded-pill text-dark"><i class="fa-solid fa-download me-2"></i>Download PDF</a>
        </div>
    </div>
</div>

<style>
/* Modern ID Card Styling */
.id-card-modern {
    width: 320px;
    background: white;
    border-radius: 20px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    position: relative;
    border: 1px solid rgba(0,0,0,0.05);
}

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
        border: 1px solid #ccc;
    }
}
</style>

<?php require_once __DIR__ . '/../../includes/dashboard_footer.php'; ?>
