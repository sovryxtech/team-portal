<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/src/Controller/AuthController.php';

$controller = new \Src\Controller\AuthController();
$controller->logout();

header("Location: " . get_base_url() . "/login.php");
exit;
