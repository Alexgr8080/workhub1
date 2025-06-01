<?php
/**
 * MyWorkHub Configuration File
 */

// Database configuration
$db_config = [
    'host'     => 'localhost', // Usually 'localhost'
    'database' => 'gotoa957_my_work_hub_db',  // Your database name (adjust if needed)
    'username' => 'gotoa957_admin',  // Your database username (adjust if needed)
    'password' => 'medo123My@', // Replace with your actual password
    'charset'  => 'utf8mb4'
];

// Site configuration
$site_config = [
    'site_name' => 'MyWorkHub',
    'base_url'  => 'https://workhub.gotoaus.com',
    'timezone'  => 'UTC', // Adjust to your timezone if needed
    'debug'     => true   // Set to false in production
];

// Set timezone
date_default_timezone_set($site_config['timezone']);

// Error handling based on debug mode
if ($site_config['debug']) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
}

/**
 * Helper function to connect to the database
 * @return PDO Database connection
 */
function get_db_connection() {
    global $db_config;
    
    try {
        $dsn = "mysql:host={$db_config['host']};dbname={$db_config['database']};charset={$db_config['charset']}";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ];
        
        return new PDO($dsn, $db_config['username'], $db_config['password'], $options);
    } catch (PDOException $e) {
        // Log the error but don't expose details
        error_log("Database connection error: " . $e->getMessage());
        throw new Exception("Database connection failed. Please check your configuration.");
    }
}
?>
