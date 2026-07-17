<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';

// Redirect to Admin Dashboard (auth checks will be handled there)
header("Location: " . get_base_url() . "/dashboard/admin/index.php");
exit;
