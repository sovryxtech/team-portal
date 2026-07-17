<?php
declare(strict_types=1);
require_once __DIR__ . '/db_connection.php';
$footerPdo = get_db_connection();
$footerComp = $footerPdo->query("SELECT * FROM companies WHERE id = 1 LIMIT 1")->fetch();
$compName = $footerComp['name'] ?? 'Sovryx Tech';
$compAddress = $footerComp['address'] ?? 'Kathmandu, Nepal';
$compContact = $footerComp['contact'] ?? '+977 1 4400000';
$emailSettings = json_decode($footerComp['email_settings'] ?? '{}', true);
$compEmail = $emailSettings['from_email'] ?? 'contact@sovryxtech.com.np';
?>
    <!-- Footer Section -->
    <footer class="text-white pt-5 pb-4" style="background-color: var(--dark-bg);">
        <div class="container text-md-start">
            <div class="row text-md-start">
                <div class="col-md-4 col-lg-4 col-xl-4 mx-auto mt-3">
                    <h5 class="text-uppercase mb-4 font-weight-bold" style="color: var(--secondary-color);"><?= htmlspecialchars($compName) ?></h5>
                    <p class="text-white-50">
                        A modern and robust system designed for secure employee record lifecycle management, digital identity issuance, and instant public verification.
                    </p>
                </div>
                <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mt-3">
                    <h5 class="text-uppercase mb-4 font-weight-bold" style="color: var(--secondary-color);">Quick Links</h5>
                    <p><a href="<?= get_base_url() ?>/index.php" class="text-white-50 text-decoration-none hover-orange">Home</a></p>
                    <p><a href="<?= get_base_url() ?>/about.php" class="text-white-50 text-decoration-none hover-orange">About Us</a></p>
                    <p><a href="<?= get_base_url() ?>/careers.php" class="text-white-50 text-decoration-none hover-orange">Careers</a></p>
                    <p><a href="<?= get_base_url() ?>/privacy.php" class="text-white-50 text-decoration-none hover-orange">Privacy Policy</a></p>
                </div>
                <div class="col-md-3 col-lg-3 col-xl-3 mx-auto mt-3">
                    <h5 class="text-uppercase mb-4 font-weight-bold" style="color: var(--secondary-color);">Contact Info</h5>
                    <p class="text-white-50"><i class="fas fa-home me-2"></i> <?= htmlspecialchars($compAddress) ?></p>
                    <p class="text-white-50"><i class="fas fa-envelope me-2"></i> <?= htmlspecialchars($compEmail) ?></p>
                    <p class="text-white-50"><i class="fas fa-phone me-2"></i> <?= htmlspecialchars($compContact) ?></p>
                </div>
            </div>
            <hr class="mb-4 bg-secondary">
            <div class="row align-items-center">
                <div class="col-md-7 col-lg-8">
                    <p class="text-white-50">© 2026 Sovryx Tech. All Rights Reserved.</p>
                </div>
                <div class="col-md-5 col-lg-4 text-md-end">
                    <a href="#" class="btn btn-outline-light btn-sm me-2"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="btn btn-outline-light btn-sm me-2"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="btn btn-outline-light btn-sm me-2"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- JS Libraries (jQuery first, then Bootstrap, then SweetAlert2) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- App Utility Script -->
    <script src="<?= get_base_url() ?>/assets/js/app.js"></script>

    <style>
        .hover-orange:hover {
            color: var(--secondary-color) !important;
            transition: 0.2s ease;
        }
    </style>
</body>
</html>
