<?php
require 'includes/db_connection.php';
require_once 'src/Controller/EmployeeController.php';

$userId = 2; // Assume user_id 2 exists
$data = [
    'category' => 'IT Support',
    'subject' => 'Test Subject',
    'description' => 'Test Description'
];
$file = null;

$controller = new \Src\Controller\EmployeeController();
$res = $controller->createSupportTicket($userId, $data, $file);

echo json_encode($res);
