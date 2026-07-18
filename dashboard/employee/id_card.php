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
                <?php 
                $desg = $details['designation_title'];
                $desgLen = strlen($desg);
                $desgFontSize = '32px';
                $desgLetterSpacing = '8px';
                if ($desgLen > 22) {
                    $desgFontSize = '18px';
                    $desgLetterSpacing = '4px';
                } elseif ($desgLen > 15) {
                    $desgFontSize = '24px';
                    $desgLetterSpacing = '6px';
                }
                ?>
                <span class="id-designation" style="font-size: <?= $desgFontSize ?>; letter-spacing: <?= $desgLetterSpacing ?>;"><?= htmlspecialchars($desg) ?></span>
            </div>

        </div>

        <!-- Action Buttons -->
        <div class="d-flex justify-content-center gap-2 mt-4 mb-5">
            <a href="<?= get_base_url() ?>/api/download_id_card.php?id=<?= $details['id'] ?>" target="_blank" class="btn btn-primary px-4 py-2"><i class="fa-solid fa-file-pdf me-2"></i>Download PDF</a>
            <button onclick="window.print();" class="btn btn-secondary-custom px-4 py-2"><i class="fa-solid fa-print me-2"></i>Print ID Card</button>
        </div>
    </div>
</div>

<!-- ========== ID Card Styles — Scaled to 638px * 1011px (1011 * 638 px) ========== -->
<style>
/* ---------- Card Shell ---------- */
.id-card {
    width: 638px;
    height: 1011px;
    background: #ffffff;
    border: 1.5px solid #bbb;
    border-radius: 12px;
    position: relative;
    margin: 0 auto;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0,0,0,.15);
    display: flex;
    flex-direction: row;
}

/* ---------- Main Content (Left) ---------- */
.id-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 35px 30px 25px 30px;
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
    width: 380px;
    filter: grayscale(100%);
}

/* ---------- Header: Logo + Company Name ---------- */
.id-header {
    display: flex;
    align-items: center;
    gap: 18px;
    width: 100%;
    margin-bottom: 25px;
}
.id-logo-icon {
    height: 80px;
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
    font-size: 36px;
    font-weight: 700;
    color: #1B2A4A;
    letter-spacing: 1px;
}
.id-company-sub {
    font-family: 'Georgia', 'Times New Roman', serif;
    font-size: 22px;
    font-weight: 700;
    color: #1B2A4A;
    letter-spacing: 2px;
    text-transform: uppercase;
    margin-top: 3px;
}

/* ---------- Circular Photo ---------- */
.id-photo-wrap {
    margin: 15px 0 20px;
}
.id-photo {
    width: 250px;
    height: 250px;
    border-radius: 50%;
    border: 7px solid #1B2A4A;
    object-fit: cover;
    background: #eee;
    display: block;
}
.id-photo-placeholder {
    display: flex !important;
    align-items: center;
    justify-content: center;
    font-size: 5.5rem;
    font-weight: 700;
    color: #fff;
    background: #1B2A4A !important;
}

/* ---------- Full Name ---------- */
.id-fullname {
    font-family: 'Georgia', 'Times New Roman', serif;
    font-size: 38px;
    font-weight: 800;
    color: #111;
    letter-spacing: 3px;
    text-align: center;
    margin-bottom: 20px;
    line-height: 1.2;
    word-spacing: 6px;
}

/* ---------- Detail Rows ---------- */
.id-details {
    width: 100%;
    text-align: left;
    font-size: 22px;
    font-family: Arial, Helvetica, sans-serif;
    color: #222;
    line-height: 2;
    margin-bottom: 20px;
}
.id-detail-row {
    padding-left: 10px;
}
.id-label {
    font-weight: 700;
}

/* ---------- QR Code ---------- */
.id-qr {
    margin: 10px 0 20px;
}
.id-qr img {
    width: 130px;
    height: 130px;
    image-rendering: pixelated;
}

/* ---------- Signature / Auth ---------- */
.id-auth {
    width: 100%;
    text-align: left;
    padding-left: 10px;
    margin-top: auto;
}
.id-signature-line {
    font-family: 'Brush Script MT', 'Segoe Script', cursive;
    font-size: 36px;
    color: #333;
    border-bottom: 2.5px solid #222;
    display: inline-block;
    padding-bottom: 2px;
    width: 200px;
    text-align: center;
    line-height: 1;
    margin-bottom: 4px;
}
.id-auth-label {
    font-size: 15px;
    font-weight: 800;
    letter-spacing: 1.5px;
    color: #111;
    text-transform: uppercase;
}

/* ---------- Footer Address ---------- */
.id-footer-address {
    width: 100%;
    text-align: center;
    font-size: 15px;
    font-weight: 700;
    color: #222;
    line-height: 1.5;
    margin-top: 25px;
    padding-top: 15px;
    border-top: 1.5px solid #ddd;
}

/* ---------- Right Sidebar ---------- */
.id-sidebar {
    width: 110px;
    min-width: 110px;
    background: #1B2A4A;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.id-designation {
    color: #ffffff;
    font-weight: 800;
    writing-mode: vertical-rl;
    text-orientation: upright;
    text-transform: uppercase;
    text-align: center;
    line-height: 1.1;
    padding: 25px 0;
    font-family: Arial, Helvetica, sans-serif;
}

/* ---------- Responsive ---------- */
@media (max-width: 680px) {
    .id-card {
        width: 100%;
        max-width: 638px;
        height: auto;
        min-height: 900px;
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
        top: 5%;
        transform: translateX(-50%);
        box-shadow: none !important;
        border: 2px solid #000;
        width: 638px;
        height: 1011px;
    }
}
</style>

<?php require_once __DIR__ . '/../../includes/dashboard_footer.php'; ?>
