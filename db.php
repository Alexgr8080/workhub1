<?php
/**
 * MyWorkHub - Database Connection
 * 
 * This file handles the database connection using PDO with proper error handling.
 * 
 * @author Dr. Ahmed AL-sadi
 * @version 1.0
 */

// Prevent direct access
if (!defined('ROOT_PATH')) {
    die('Direct access not allowed');
}

// Include config file if not already included
if (!defined('DB_HOST')) {
    require_once ROOT_PATH . '/includes/config.php';
}

// Global database connection variable
$pdo = null;

/**
 * Get database connection
 * @return PDO Database connection object
 * @throws Exception If connection fails
 */
function getConnection() {
    global $pdo;
    
    if ($pdo === null) {
        try {
            // Create DSN (Data Source Name)
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            
            // PDO options for better security and error handling
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ];
            
            // Create PDO connection
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            
        } catch (PDOException $e) {
            // Log the actual error (in production, don't show to user)
            error_log("Database Connection Error: " . $e->getMessage());
            
            // Show user-friendly error message
            if (ini_get('display_errors')) {
                die("Database connection failed: " . $e->getMessage());
            } else {
                die("Database connection failed. Please contact administrator.");
            }
        }
    }
    
    return $pdo;
}

/**
 * Test database connection
 * @return bool True if connection successful
 */
function testConnection() {
    try {
        $pdo = getConnection();
        $stmt = $pdo->query("SELECT 1");
        return $stmt !== false;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Execute a prepared statement safely
 * @param string $sql SQL query
 * @param array $params Parameters for the query
 * @return PDOStatement|false
 */
function executeQuery($sql, $params = []) {
    try {
        $pdo = getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        error_log("Query execution error: " . $e->getMessage());
        return false;
    }
}

// Initialize connection on first load
getConnection();

?>