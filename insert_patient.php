<?php
require 'includes/db.php';
$password = password_hash('1234', PASSWORD_DEFAULT);
$pdo->exec("INSERT IGNORE INTO patients (dni, first_name, last_name, password_hash) VALUES ('12345678', 'Juan', 'Perez', '$password')");
$pdo->exec("INSERT IGNORE INTO exam_results (patient_id, test_id, status, date_issued) VALUES (1, 1, 'completed', NOW()), (1, 2, 'pending', NOW())");
echo 'Patient created';
?>
