<?php
declare(strict_types=1);
$pageTitle = "Careers at Sovryx Tech";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/db_connection.php';

$pdo = get_db_connection();
$careers = $pdo->query("SELECT c.*, d.name as dept_name, b.name as branch_name FROM careers c JOIN departments d ON c.department_id = d.id JOIN branches b ON c.branch_id = b.id WHERE c.status = 'Active' ORDER BY c.id DESC")->fetchAll();
?>

<section class="py-5 bg-light">
    <div class="container py-4">
        <div class="text-center mb-5">
            <h2 class="text-primary font-weight-bold">Career Opportunities</h2>
            <p class="text-secondary w-50 mx-auto">Explore open job listings and submit your profile registration online to begin onboarding.</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php if (empty($careers)): ?>
                    <div class="alert alert-info text-center rounded-3">Currently, there are no open positions. Please check back later!</div>
                <?php else: ?>
                    <?php foreach ($careers as $car): ?>
                        <div class="card-custom p-4 bg-white mb-3">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <h4 class="text-primary mb-1"><?= htmlspecialchars($car['title']) ?></h4>
                                    <p class="text-muted small mb-0"><i class="fa-solid fa-map-pin me-1"></i> <?= htmlspecialchars($car['branch_name']) ?> (<?= htmlspecialchars($car['dept_name']) ?>) | <?= htmlspecialchars($car['type']) ?></p>
                                </div>
                                <a href="<?= get_base_url() ?>/register.php" class="btn btn-secondary-custom"><i class="fa-solid fa-user-pen me-2"></i>Apply & Register</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
