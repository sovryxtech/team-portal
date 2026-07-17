<?php
declare(strict_types=1);
$pageTitle = "Privacy Policy";
require_once __DIR__ . '/includes/header.php';
?>

<section class="py-5 bg-light">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card-custom p-4 p-md-5 bg-white">
                    <h2 class="text-primary font-weight-bold mb-4">Privacy Policy</h2>
                    <p class="text-muted">Last Updated: July 2026</p>
                    <hr class="mb-4">
                    
                    <h4 class="text-primary mt-4">1. Information Collection</h4>
                    <p class="text-secondary small">
                        We collect applicant files, national IDs, CVs, and police clearance reports solely to execute employment eligibility validation and register secure member directories inside the employee management system.
                    </p>
                    
                    <h4 class="text-primary mt-4">2. Public ID Validation & QR Logs</h4>
                    <p class="text-secondary small">
                        Public search verifications via `/verify` or QR badge scans log transaction metadata (timestamp, scanning IP, and browser user agent) for system audit integrity and security monitoring against fraud.
                    </p>

                    <h4 class="text-primary mt-4">3. Data Integrity & Control</h4>
                    <p class="text-secondary small">
                        All uploaded assets are stored inside an execute-disabled directory structure. Passwords utilize BCRYPT cryptography, and operations run prepared statements to guarantee strict database confidentiality.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
