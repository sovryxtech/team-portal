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
    <div class="col-lg-7 text-center">
        <h5 class="text-secondary mb-4"><i class="fa-solid fa-id-badge me-2"></i>Official Corporate Identity Card</h5>

        <!-- ===== Digital ID Card — Exact Replica of Physical Sovryx Tech Card ===== -->
        <div class="id-card" id="printableIdCard">

            <!-- Faint watermark logo behind content -->
            <div class="id-watermark">
                <img src="<?= get_base_url() ?>/images/LOGO.png" alt="">
            </div>

            <!-- Main Content Area (left of sidebar) -->
            <div class="id-main">

                <!-- Top: Logo + Company Name -->
                <div class="id-header">
                    <img src="<?= get_base_url() ?>/images/LOGO.png" alt="Sovryx Tech" class="id-logo-icon">
                    <div class="id-company-text">
                        <span class="id-company-name">Sovryx Tech</span>
                        <span class="id-company-sub">Pvt. Ltd.</span>
                    </div>
                </div>

                <!-- Circular Employee Photo -->
                <div class="id-photo-wrap">
                    <?php if (!empty($details['profile_photo'])): ?>
                        <img src="<?= get_base_url() . '/' . $details['profile_photo'] ?>" alt="Photo" class="id-photo">
                    <?php else: ?>
                        <div class="id-photo id-photo-placeholder">
                            <?= strtoupper(substr($details['full_name'], 0, 2)) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Employee Full Name -->
                <div class="id-fullname"><?= htmlspecialchars(strtoupper($details['full_name'])) ?></div>

                <!-- Detail Rows -->
                <div class="id-details">
                    <div class="id-detail-row"><span class="id-label">ID NO:</span> <?= htmlspecialchars($details['employee_custom_id']) ?></div>
                    <div class="id-detail-row"><span class="id-label">Address:</span> <?= htmlspecialchars($details['address'] ?? $details['branch_name']) ?></div>
                    <div class="id-detail-row"><span class="id-label">Contact:</span> <?= htmlspecialchars($details['phone'] ?? 'N/A') ?></div>
                    <div class="id-detail-row"><span class="id-label">Email:</span> <?= htmlspecialchars($details['email']) ?></div>
                </div>

                <!-- QR Code (replaces barcode from physical card) -->
                <div class="id-qr">
                    <img src="<?= $qrDataUri ?>" alt="Verification QR">
                </div>

                <!-- Signature + Authorized -->
                <div class="id-auth">
                    <div class="id-signature-line">Approved</div>
                    <div class="id-auth-label">AUTHORIZED BY CEO</div>
                </div>

                <!-- Bottom Company Address -->
                <div class="id-footer-address">
                    Biratnagar-1, Morang, Koshi Province, Nepal<br>
                    Email: sovryx.tech@gmail.com
                </div>

            </div>

            <!-- Right Sidebar with Vertical Designation -->
            <div class="id-sidebar">
                <span class="id-designation"><?= htmlspecialchars($details['designation_title']) ?></span>
            </div>

        </div>

        <!-- Action Buttons -->
        <div class="d-flex justify-content-center gap-2 mt-4 mb-5">
            <a href="<?= get_base_url() ?>/api/download_id_card.php?id=<?= $details['id'] ?>" target="_blank" class="btn btn-primary px-4 py-2"><i class="fa-solid fa-file-pdf me-2"></i>Download PDF</a>
            <button onclick="window.print();" class="btn btn-secondary-custom px-4 py-2"><i class="fa-solid fa-print me-2"></i>Print ID Card</button>
        </div>
    </div>
</div>

<!-- ========== ID Card Styles — Exact Physical Card Replica ========== -->
<style>
/* ---------- Card Shell ---------- */
.id-card {
    width: 340px;
    min-height: 540px;
    background: #ffffff;
    border: 1px solid #bbb;
    border-radius: 6px;
    position: relative;
    margin: 0 auto;
    overflow: hidden;
    box-shadow: 0 6px 24px rgba(0,0,0,.12), 0 2px 8px rgba(0,0,0,.08);
    display: flex;
    flex-direction: row;
}

/* ---------- Main Content (Left) ---------- */
.id-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 18px 14px 12px 14px;
    position: relative;
    z-index: 1;
    min-width: 0;
}

/* ---------- Watermark ---------- */
.id-watermark {
    position: absolute;
    top: 50%;
    left: 42%;
    transform: translate(-50%, -50%);
    opacity: 0.04;
    z-index: 0;
    pointer-events: none;
}
.id-watermark img {
    width: 200px;
    filter: grayscale(100%);
}

/* ---------- Header: Logo + Company Name ---------- */
.id-header {
    display: flex;
    align-items: center;
    gap: 10px;
    width: 100%;
    margin-bottom: 12px;
}
.id-logo-icon {
    height: 44px;
    width: auto;
    object-fit: contain;
}
.id-company-text {
    display: flex;
    flex-direction: column;
    line-height: 1.15;
}
.id-company-name {
    font-family: 'Georgia', 'Times New Roman', serif;
    font-size: 20px;
    font-weight: 700;
    color: #1B2A4A;
    letter-spacing: 0.5px;
}
.id-company-sub {
    font-family: 'Georgia', 'Times New Roman', serif;
    font-size: 13px;
    font-weight: 700;
    color: #1B2A4A;
    letter-spacing: 1.2px;
    text-transform: uppercase;
    margin-top: 1px;
}

/* ---------- Circular Photo ---------- */
.id-photo-wrap {
    margin: 6px 0 8px;
}
.id-photo {
    width: 130px;
    height: 130px;
    border-radius: 50%;
    border: 4px solid #1B2A4A;
    object-fit: cover;
    background: #eee;
    display: block;
}
.id-photo-placeholder {
    display: flex !important;
    align-items: center;
    justify-content: center;
    font-size: 2.8rem;
    font-weight: 700;
    color: #fff;
    background: #1B2A4A !important;
}

/* ---------- Full Name ---------- */
.id-fullname {
    font-family: 'Georgia', 'Times New Roman', serif;
    font-size: 20px;
    font-weight: 800;
    color: #111;
    letter-spacing: 1.8px;
    text-align: center;
    margin-bottom: 8px;
    line-height: 1.2;
    word-spacing: 4px;
}

/* ---------- Detail Rows ---------- */
.id-details {
    width: 100%;
    text-align: left;
    font-size: 12.5px;
    font-family: Arial, Helvetica, sans-serif;
    color: #222;
    line-height: 1.75;
    margin-bottom: 8px;
}
.id-detail-row {
    padding-left: 4px;
}
.id-label {
    font-weight: 700;
}

/* ---------- QR Code ---------- */
.id-qr {
    margin: 2px 0 6px;
}
.id-qr img {
    width: 78px;
    height: 78px;
    image-rendering: pixelated;
}

/* ---------- Signature / Auth ---------- */
.id-auth {
    width: 100%;
    text-align: left;
    padding-left: 4px;
    margin-top: auto;
}
.id-signature-line {
    font-family: 'Brush Script MT', 'Segoe Script', cursive;
    font-size: 22px;
    color: #333;
    border-bottom: 1.5px solid #222;
    display: inline-block;
    padding-bottom: 1px;
    width: 110px;
    text-align: center;
    line-height: 1;
    margin-bottom: 2px;
}
.id-auth-label {
    font-size: 8.5px;
    font-weight: 800;
    letter-spacing: 0.8px;
    color: #111;
    text-transform: uppercase;
}

/* ---------- Footer Address ---------- */
.id-footer-address {
    width: 100%;
    text-align: center;
    font-size: 8.5px;
    font-weight: 700;
    color: #222;
    line-height: 1.5;
    margin-top: 8px;
    padding-top: 6px;
    border-top: 1px solid #ddd;
}

/* ---------- Right Sidebar ---------- */
.id-sidebar {
    width: 56px;
    min-width: 56px;
    background: #1B2A4A;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.id-designation {
    color: #ffffff;
    font-size: 22px;
    font-weight: 800;
    letter-spacing: 4px;
    writing-mode: vertical-rl;
    text-orientation: upright;
    text-transform: uppercase;
    text-align: center;
    line-height: 1;
    padding: 14px 0;
    font-family: Arial, Helvetica, sans-serif;
}

/* ---------- Responsive ---------- */
@media (max-width: 400px) {
    .id-card {
        width: 95vw;
        min-height: 480px;
    }
    .id-designation {
        font-size: 16px;
        letter-spacing: 2px;
    }
    .id-sidebar {
        width: 44px;
        min-width: 44px;
    }
}
</style>

<!-- Print Styles -->
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
        top: 10%;
        transform: translateX(-50%);
        box-shadow: none !important;
        border: 1.5px solid #000;
    }
}
</style>

<?php require_once __DIR__ . '/../../includes/dashboard_footer.php'; ?>
