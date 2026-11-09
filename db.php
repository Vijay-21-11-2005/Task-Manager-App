<?php
// Database connection settings (XAMPP MariaDB)
$host = 'localhost';
$port = 3306;            // verified from XAMPP Control Panel
$dbname = 'task_manager';
$user = 'root';          // XAMPP default user
$pass = '';              // XAMPP default: empty password

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}