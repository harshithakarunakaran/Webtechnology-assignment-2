<?php
declare(strict_types=1);

// Change these values to match your local XAMPP MySQL setup.
$dbHost = '127.0.0.1';
$dbName = 'student_portal';
$dbUser = 'root';
$dbPass = ''; // Default XAMPP setup often has an empty password; set yours if configured.

try {
    $pdo = new PDO(
        "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4",
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $exception) {
    // Do not expose database credentials or exception details to visitors.
    http_response_code(500);
    exit('Database connection failed. Check config.php and confirm MySQL is running.');
}

session_start();
