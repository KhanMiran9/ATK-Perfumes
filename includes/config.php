<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'perfume_store');
define('DB_USER', 'root');
define('DB_PASS', '');

// Site configuration
define('BASE_URL', 'http://localhost/perfume-store/');
define('UPLOAD_PATH', __DIR__ . '/../assets/uploads/');

// Enable error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set timezone
date_default_timezone_set('UTC');

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>