<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/src/Controller/AuthController.php';

// Redirect if already logged in
if (auth_check()) {
    header("Location: " . get_base_url() . "/dashboard/index.php");
    exit;
}

$error = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF check
    if (!csrf_verify()) {
        $error = 'CSRF verification failed. Request blocked.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $captcha = trim($_POST['captcha_phrase'] ?? '');
        
        $authController = new \Src\Controller\AuthController();
        $result = $authController->login($username, $password, $captcha);
        
        if ($result['success']) {
            // Check for previous redirect URL
            $redirectUrl = $_SESSION['redirect_url'] ?? (get_base_url() . '/dashboard/index.php');
            unset($_SESSION['redirect_url']);
            
            header("Location: " . $redirectUrl);
            exit;
        } else {
            $error = $result['message'];
        }
    }
}

$pageTitle = "Secure Login";
require_once __DIR__ . '/includes/header.php';
?>

<section class="py-5 bg-light" style="min-height: 80vh;">
    <div class="container">
        <div class="row justify-content-center mt-4">
            <div class="col-md-5">
                <div class="card-custom p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="fa-solid fa-shield-halved" style="font-size: 2rem;"></i>
                        </div>
                        <h3 class="text-primary font-weight-bold">Member Portal Access</h3>
                        <p class="text-secondary small">Access employee dashboards and audit directories.</p>
                    </div>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
                            <i class="fa-solid fa-triangle-exclamation me-2"></i><?= htmlspecialchars($error) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="login.php">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label class="form-label">Username or Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                                <input type="text" name="username" class="form-control" placeholder="john.doe" value="<?= htmlspecialchars($username) ?>" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                            </div>
                        </div>

                        <div class="mb-4 captcha-container">
                            <label class="form-label">Security Check (CAPTCHA)</label>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <img src="<?= get_base_url() ?>/api/auth_captcha.php" alt="CAPTCHA" class="captcha-img rounded-3 border">
                                <button type="button" class="btn btn-outline-secondary btn-sm refresh-captcha"><i class="fa-solid fa-rotate"></i> Refresh</button>
                            </div>
                            <input type="text" name="captcha_phrase" class="form-control" placeholder="Enter CAPTCHA code" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 font-weight-bold"><i class="fa-solid fa-arrow-right-to-bracket me-2"></i>Sign In</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
