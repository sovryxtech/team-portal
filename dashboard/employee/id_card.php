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
            <!-- Background Watermark -->
            <div class="id-card-bg-watermark">
                <img src="<?= get_base_url() ?>/images/LOGO.png" alt="Watermark">
            </div>

            <!-- Header / Logo -->
            <div class="id-card-header">
                <img src="<?= get_base_url() ?>/images/LOGO.png" alt="Sovryx Tech Logo" class="id-card-logo-img">
            </div>

            <!-- Right Banner -->
            <div class="id-card-right-banner">
                <div class="designation-text"><?= htmlspecialchars($details['designation_title']) ?></div>
            </div>

            <!-- Profile Photo -->
            <div class="id-card-photo-container">
                <?php if (!empty($details['profile_photo'])): ?>
                    <img src="<?= get_base_url() . '/' . $details['profile_photo'] ?>" alt="Employee Photo" class="id-card-photo">
                <?php else: ?>
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center border border-white border-4 shadow-sm id-card-photo" style="font-size: 2.5rem; font-weight: 600; margin: 0 auto; display: inline-flex !important;">
                        <?= strtoupper(substr($details['full_name'], 0, 2)) ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Name -->
            <div class="id-card-name">
                <?= htmlspecialchars($details['full_name']) ?>
            </div>
            
            <!-- Details -->
            <div class="id-card-details-box">
                <div class="mb-1"><strong>ID NO:</strong> <?= htmlspecialchars($details['employee_custom_id']) ?></div>
                <div class="mb-1"><strong>Address:</strong> <?= htmlspecialchars($details['address'] ?? $details['branch_name']) ?></div>
                <div class="mb-1"><strong>Contact:</strong> <?= htmlspecialchars($details['phone'] ?? 'N/A') ?></div>
                <div class="mb-1"><strong>Email:</strong> <?= htmlspecialchars($details['email']) ?></div>
            </div>

            <!-- QR Code -->
            <div class="id-card-qr-container">
                <img src="<?= $qrDataUri ?>" alt="QR Code">
            </div>

            <!-- Signature -->
            <div class="id-card-signature">
                <div class="id-card-signature-img">Approved</div>
                <div>AUTHORIZED BY CEO</div>
            </div>

            <!-- Footer -->
            <div class="id-card-footer">
                <?= htmlspecialchars($details['address'] ?? $details['branch_name']) ?>, Nepal<br>
                Email: sovryx.tech@gmail.com
            </div>
        </div>

        <style>
            .id-card-container {
                width: 320px;
                height: 500px;
                background: #ffffff;
                border: 1px solid #ccc;
                position: relative;
                margin: 0 auto;
                font-family: Arial, sans-serif;
                color: #000;
                overflow: hidden;
                box-sizing: border-box;
                text-align: left;
            }

            .id-card-right-banner {
                position: absolute;
                right: 0;
                top: 0;
                width: 60px;
                height: 100%;
                background-color: #a98f3b;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .id-card-right-banner .designation-text {
                color: white;
                font-size: 32px;
                font-weight: bold;
                letter-spacing: 5px;
                writing-mode: vertical-rl;
                text-orientation: upright;
                text-transform: uppercase;
                text-align: center;
            }

            .id-card-header {
                padding: 15px;
                position: relative;
                z-index: 1;
            }

            .id-card-logo-img {
                height: 45px;
                object-fit: contain;
                margin-left: 5px;
            }

            .id-card-photo-container {
                text-align: center;
                margin-top: 10px;
                z-index: 1;
                position: relative;
                padding-right: 60px;
            }

            .id-card-photo {
                width: 140px;
                height: 140px;
                border-radius: 50%;
                border: 4px solid #a98f3b;
                object-fit: cover;
                background: white;
            }

            .id-card-name {
                text-align: center;
                font-family: Georgia, "Times New Roman", Times, serif;
                font-size: 22px;
                font-weight: 800;
                font-variant: small-caps;
                margin-top: 10px;
                margin-bottom: 15px;
                letter-spacing: 1.5px;
                padding-right: 60px;
            }

            .id-card-details-box {
                padding-left: 15px;
                font-size: 11px;
                line-height: 1.6;
                margin-bottom: 10px;
                width: 250px;
            }

            .id-card-details-box strong {
                font-weight: 600;
            }

            .id-card-qr-container {
                padding-left: 15px;
                margin-bottom: 5px;
            }

            .id-card-qr-container img {
                width: 80px;
                height: 80px;
            }

            .id-card-signature {
                padding-left: 15px;
                font-size: 9px;
                font-weight: bold;
                margin-top: 5px;
            }

            .id-card-signature-img {
                font-family: 'Brush Script MT', cursive;
                font-size: 20px;
                line-height: 1;
                margin-bottom: 2px;
                border-bottom: 1px solid #000;
                display: inline-block;
                padding-bottom: 2px;
                width: 100px;
                text-align: center;
                color: #333;
                font-weight: normal;
            }

            .id-card-footer {
                position: absolute;
                bottom: 10px;
                left: 0;
                width: 260px;
                text-align: center;
                font-size: 9px;
                font-weight: bold;
            }

            .id-card-bg-watermark {
                position: absolute;
                top: 50%;
                left: 130px;
                transform: translate(-50%, -50%);
                opacity: 0.05;
                z-index: 0;
            }

            .id-card-bg-watermark img {
                width: 220px;
                filter: grayscale(100%);
            }
        </style>

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
