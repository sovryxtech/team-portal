<?php
require 'includes/db_connection.php';
$pdo = get_db_connection();
$stmt = $pdo->query('DESCRIBE support_tickets');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
