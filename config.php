<?php
/**
 * MyWorkHub - Configuration File
 * * This file contains all the configuration settings for the application.
 * Update the database credentials with your HostPapa database details.
 * * @author Dr. Ahmed AL-sadi
 * @version 1.1 (Added $db_config array for compatibility)
 */

// Prevent direct access - THIS CHECK IS IMPORTANT
// The file including this config.php MUST define ROOT_PATH first.
if (!defined('ROOT_PATH')) {
    // It's generally better to log this error or handle it more gracefully
    // in a production environment than just die(), but for now, this is the check.
    error_log('config.php included without ROOT_PATH defined. Access attempt from: ' . ($_SERVER['SCRIPT_FILENAME'] ?? 'Unknown script'));
    http_response_code(500); // Internal Server Error
    // To avoid breaking JSON responses if this file is included by an API endpoint that hasn't set ROOT_PATH:
    if (php_sapi_name() !== 'cli' && !headers_sent()) {
        header('Content-Type: application/json');
    }
    echo json_encode([
        'status' => 'error', 
        'message' => 'Configuration error: ROOT_PATH not defined. Direct access to config or misconfiguration in including file.'
    ]);
    die(); // Stop execution if ROOT_PATH is not defined.
}

// Database Configuration - UPDATE THESE WITH YOUR HOSTPAPA DETAILS
define('DB_HOST', 'localhost'); //
define('DB_NAME', 'gotoa957_my_work_hub_db'); // e.g., myworkhub
define('DB_USER', 'gotoa957_admin'); //
define('DB_PASS', 'medo123My@'); // !!! IMPORTANT: Replace with your real password !!!
define('DB_CHARSET', 'utf8mb4');               // Character set

// Site Configuration
define('SITE_NAME', 'MyWorkHub'); //
define('SITE_URL', 'https://workhub.gotoaus.com');  // Update with your domain
define('SITE_EMAIL', 'admin@workhub.gotoaus.com'); //

// Security Settings
define('SECURE_KEY', 'your-secret-key-here-change-this-to-random-string'); //
define('SESSION_TIMEOUT', 3600); // 1 hour in seconds
define('MAX_LOGIN_ATTEMPTS', 5); //
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes

// File Upload Settings
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024); // 10MB
// Ensure UPLOAD_PATH uses the ROOT_PATH correctly.
// If ROOT_PATH is already the absolute path to your web root, UPLOAD_PATH might be:
// define('UPLOAD_PATH', ROOT_PATH . '/uploads/');
// Or if your uploads folder is outside the public web root (safer):
// define('UPLOAD_PATH', dirname(ROOT_PATH) . '/uploads_private/'); // Example if ROOT_PATH is public_html
// For now, using the provided one:
define('UPLOAD_PATH', ROOT_PATH . '/uploads/'); //
define('ALLOWED_EXTENSIONS', 'jpg,jpeg,png,gif,pdf,doc,docx,txt'); //

// Error Reporting (Set to 0 for production eventually)
ini_set('display_errors', 1); //
ini_set('display_startup_errors', 1); //
error_reporting(E_ALL); //

// Timezone Setting
date_default_timezone_set('Australia/Sydney'); // Change to your timezone

// For compatibility: Create the $db_config array from defined constants.
// The API scripts provided in previous responses are designed to use this array
// or fall back to the constants.
global $db_config; // Make it global if some old includes might expect it this way.
$db_config = [
    'host'     => DB_HOST,
    'database' => DB_NAME,
    'username' => DB_USER,
    'password' => DB_PASS,
    'charset'  => DB_CHARSET
];

// Start session if not already started
// Note: API endpoints that are sessionless might not need this,
// but it's generally harmless if sessions are managed properly.
if (php_sapi_name() !== 'cli' && session_status() === PHP_SESSION_NONE) { //
    // Consider session cookie parameters for security if not already set elsewhere
    // session_set_cookie_params(['lifetime' => SESSION_TIMEOUT, 'httponly' => true, 'samesite' => 'Lax']);
    session_start(); //
}

?>