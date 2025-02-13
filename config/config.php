<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'blood_donation1');
define('DB_USER', 'root');
define('DB_PASS', '');

// Application configuration
define('SITE_NAME', 'Blood Donation Management System');
define('BASE_URL', 'http://localhost/blood');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Time zone
date_default_timezone_set('UTC');

// File upload settings
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_FILE_TYPES', ['jpg', 'jpeg', 'png', 'pdf']);

// Initialize session if not already started
function init_session() {
    if (session_status() === PHP_SESSION_NONE) {
        // Set session parameters before starting the session
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
        
        session_start();
    }
}
?> 