<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db_connection.php';

try {
    $pdo = get_db_connection();

    $sql = "
    CREATE TABLE IF NOT EXISTS `support_tickets` (
      `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
      `user_id` int(10) UNSIGNED NOT NULL,
      `ticket_number` varchar(20) NOT NULL,
      `category` varchar(100) NOT NULL,
      `subject` varchar(255) NOT NULL,
      `description` text NOT NULL,
      `status` enum('Pending', 'In Progress', 'Resolved', 'Closed') DEFAULT 'Pending',
      `attachment` varchar(255) DEFAULT NULL,
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
      PRIMARY KEY (`id`),
      UNIQUE KEY `ticket_number` (`ticket_number`),
      KEY `user_id` (`user_id`),
      CONSTRAINT `fk_support_tickets_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";

    $pdo->exec($sql);`
    echo "support_tickets table created successfully.\n";

} catch (Exception $e) {
    echo "Error creating support_tickets table: " . $e->getMessage() . "\n";
}
