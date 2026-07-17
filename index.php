<?php
declare(strict_types=1);
$pageTitle = "Modern HR Management & Verification";
require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<header class="hero-section">
    <div class="container text-center py-5">
        <h1 class="display-4 font-weight-bold text-white mb-3" style="font-family: 'Poppins', sans-serif; font-weight: 800;">
            Securing Corporate Identities & Workforces
        </h1>
        <p class="lead text-white-50 mb-5 mx-auto w-75">
            The ultimate modern Employee Management, Digital ID Generation, and Instant Public QR Verification system built for reliable scale.
        </p>
        <div class="d-flex justify-content-center gap-3">
            <a href="<?= get_base_url() ?>/register.php" class="btn btn-secondary-custom btn-lg px-4 py-3"><i class="fa-solid fa-user-pen me-2"></i>Apply for Registration</a>
            <a href="<?= get_base_url() ?>/verify.php" class="btn btn-outline-light btn-lg px-4 py-3"><i class="fa-solid fa-qrcode me-2"></i>Verify Employee Status</a>
        </div>
    </div>
</header>

<!-- Feature Section -->
<section class="py-5 bg-white">
    <div class="container py-4">
        <div class="text-center mb-5">
            <h2 class="text-primary font-weight-bold">System Features & Modules</h2>
            <p class="text-secondary w-50 mx-auto">Engineered natively to secure operations and streamline corporate HR identity services.</p>
        </div>
        
        <div class="row g-4">
            <!-- Feature 1 -->
            <div class="col-md-4">
                <div class="card-custom p-4 text-center h-100 bg-light">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-4 shadow" style="width: 70px; height: 70px;">
                        <i class="fa-solid fa-route" style="font-size: 1.8rem;"></i>
                    </div>
                    <h4>Multi-Step Onboarding</h4>
                    <p class="text-secondary small">
                        Smooth, validated self-registration wizard allowing applicants to upload credentials, citizenship files, and resumes seamlessly.
                    </p>
                </div>
            </div>

            <!-- Feature 2 -->
            <div class="col-md-4">
                <div class="card-custom p-4 text-center h-100 bg-light">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-4 shadow" style="width: 70px; height: 70px;">
                        <i class="fa-solid fa-qrcode" style="font-size: 1.8rem;"></i>
                    </div>
                    <h4>QR Code Verification</h4>
                    <p class="text-secondary small">
                        Public-facing instant verification screen. Scan QR code from employee badges or type in ID codes directly.
                    </p>
                </div>
            </div>

            <!-- Feature 3 -->
            <div class="col-md-4">
                <div class="card-custom p-4 text-center h-100 bg-light">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-4 shadow" style="width: 70px; height: 70px;">
                        <i class="fa-solid fa-id-card" style="font-size: 1.8rem;"></i>
                    </div>
                    <h4>Digital ID Generator</h4>
                    <p class="text-secondary small">
                        Automatically compile standard printable vertical PVC employee ID badges, embedding custom QR codes and company logos.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-5 text-white bg-gradient-primary">
    <div class="container py-4 text-center">
        <h3 class="font-weight-bold mb-3">Looking to Join Our Workforce?</h3>
        <p class="text-white-50 mb-4 w-50 mx-auto">Submit your profile application, academic logs, and clearance files online in few simple steps.</p>
        <a href="<?= get_base_url() ?>/register.php" class="btn btn-secondary-custom btn-lg px-5 py-3"><i class="fa-solid fa-right-to-bracket me-2"></i>Register Profile Now</a>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
