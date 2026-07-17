<?php
declare(strict_types=1);
$pageTitle = "Careers at Sovryx Tech";
require_once __DIR__ . '/includes/header.php';
?>

<section class="py-5 bg-light">
    <div class="container py-4">
        <div class="text-center mb-5">
            <h2 class="text-primary font-weight-bold">Career Opportunities</h2>
            <p class="text-secondary w-50 mx-auto">Explore open job listings and submit your profile registration online to begin onboarding.</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Job Card 1 -->
                <div class="card-custom p-4 bg-white mb-3">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h4 class="text-primary mb-1">Senior Web Developer</h4>
                            <p class="text-muted small mb-0"><i class="fa-solid fa-map-pin me-1"></i> Kathmandu HQ | Full-Time</p>
                        </div>
                        <a href="<?= get_base_url() ?>/register.php" class="btn btn-secondary-custom"><i class="fa-solid fa-user-pen me-2"></i>Apply & Register</a>
                    </div>
                </div>

                <!-- Job Card 2 -->
                <div class="card-custom p-4 bg-white mb-3">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h4 class="text-primary mb-1">UI/UX Designer</h4>
                            <p class="text-muted small mb-0"><i class="fa-solid fa-map-pin me-1"></i> Kathmandu HQ | Full-Time</p>
                        </div>
                        <a href="<?= get_base_url() ?>/register.php" class="btn btn-secondary-custom"><i class="fa-solid fa-user-pen me-2"></i>Apply & Register</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
