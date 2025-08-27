<?php
// Enhanced database connection with security
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'Blog';

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    error_log("Database connection failed: " . mysqli_connect_error());
    die("Database connection failed. Please try again later.");
}

// Set charset for security
mysqli_set_charset($conn, "utf8mb4");

// Secure session configuration
if (session_status() === PHP_SESSION_NONE) {
    // Configure secure session settings
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_samesite', 'Strict');
    
    // Only use secure cookies in production (HTTPS)
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        ini_set('session.cookie_secure', 1);
    }
    
    session_start();
}
