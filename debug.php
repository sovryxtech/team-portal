<?php
$pdo = new PDO("mysql:host=127.0.0.1", "root", "", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$stmt = $pdo->query("SHOW ENGINE INNODB STATUS");
$res = $stmt->fetch(PDO::FETCH_ASSOC);
$status = $res['Status'];
// Extract LATEST FOREIGN KEY ERROR
if (preg_match('/LATEST FOREIGN KEY ERROR\n[-]+\n(.*?)\n[-]+/s', $status, $matches)) {
    echo "LATEST FOREIGN KEY ERROR:\n" . $matches[1] . "\n";
} else {
    echo "No latest foreign key error found in InnoDB status.\n";
}
