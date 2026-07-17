<?php
declare(strict_types=1);

/**
 * PDO Database Connection Helper
 */

function get_db_connection(): PDO {
    static $pdo = null;
    
    if ($pdo === null) {
        $configPath = __DIR__ . '/../config/database.php';
        if (!file_exists($configPath)) {
            throw new RuntimeException("Database configuration file not found at: " . $configPath);
        }
        
        $config = require $configPath;
        
        $dsn = sprintf(
            "mysql:host=%s;port=%d;dbname=%s;charset=%s",
            $config['host'],
            $config['port'],
            $config['dbname'],
            $config['charset']
        );
        
        try {
            $pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);
        } catch (PDOException $e) {
            throw new RuntimeException("Database connection failed: " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }
    
    return $pdo;
}

// Instantiate globally when included
try {
    $pdo = get_db_connection();
} catch (Exception $e) {
    // If not in CLI, output user friendly message
    if (php_sapi_name() !== 'cli') {
        http_response_code(500);
        die("Critical Error: Unable to connect to database. " . htmlspecialchars($e->getMessage()));
    } else {
        throw $e;
    }
}
