<?php
/**
 * MyWorkHub - Core Functions (includes/functions.php)
 *
 * This file contains core utility functions, authentication helpers,
 * database interaction wrappers, and other essential functions for the application.
 *
 * It is included by `includes/auth.php`.
 * It relies on `includes/db.php` for the $pdo connection and executeQuery().
 * It relies on `includes/config.php` for constants like SITE_URL.
 *
 * @author Dr. Ahmed AL-sadi (Revised by AI)
 * @version 1.1
 */

// Prevent direct access if ROOT_PATH is not defined (though auth.php should define it first)
if (!defined('ROOT_PATH')) {
    // This is a fallback, ideally auth.php or the entry script (index.php) defines ROOT_PATH.
    // If this functions.php is included directly without ROOT_PATH, this might be an issue.
    // However, since auth.php includes this, ROOT_PATH should be available.
    // die('Direct access not allowed to functions.php');
}

// --- Session & Authentication Helper Functions ---

/**
 * Check if the user is logged in.
 * Relies on 'user_id' being set in the session by createUserSession() in auth/session.php.
 * @return bool True if logged in, false otherwise.
 */
function isLoggedIn() {
    if (session_status() === PHP_SESSION_NONE) {
        // This case should ideally not happen if auth.php -> auth/session.php runs first.
        // However, as a safeguard if functions.php is included in a context where session isn't auto-started by session.php
        // error_log("isLoggedIn called before session started. Ensure auth/session.php runs first.");
        // For robustness, you might want to ensure session.php's initSession() is called.
        // But this indicates an include order problem.
    }
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get the ID of the currently logged-in user.
 * @return int|null User ID if logged in, null otherwise.
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get the username of the currently logged-in user.
 * @return string|null Username if logged in, null otherwise.
 */
function getCurrentUsername() {
    return $_SESSION['username'] ?? null;
}

/**
 * Get the role of the currently logged-in user.
 * @return string|null User role if logged in, null otherwise.
 */
function getCurrentUserRole() {
    return $_SESSION['user_role'] ?? null;
}

/**
 * Check if the current user has a specific role or one of an array of roles.
 * @param string|array $role Role name (e.g., 'admin') or an array of role names.
 * @return bool True if the user has the role, false otherwise.
 */
function hasRole($role) {
    $userRole = getCurrentUserRole();
    if (!$userRole) {
        return false; // Not logged in or role not set
    }
    if (is_array($role)) {
        return in_array($userRole, $role);
    }
    return $userRole === $role;
}

/**
 * Handles user login by verifying credentials and creating a session.
 * Relies on getRecord(), verifyPassword() from this file (or db.php),
 * and createUserSession() from auth/session.php.
 *
 * @param string $emailOrUsername The user's email or username.
 * @param string $password The user's plain text password.
 * @return bool True on successful login, false otherwise.
 */
function loginUser($emailOrUsername, $password) {
    // $pdo should be globally available from db.php
    // Determine if login is with email or username
    $field = filter_var($emailOrUsername, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

    $sql = "SELECT * FROM Users WHERE {$field} = ? AND is_active = TRUE";
    $user = getRecord($sql, [$emailOrUsername]); // Uses getRecord defined later in this file

    if ($user && verifyPassword($password, $user['password'])) { // Uses verifyPassword
        // Password is correct, create session
        // createUserSession() is defined in auth/session.php and should be available
        if (function_exists('createUserSession')) {
            createUserSession($user, isset($_POST['remember_me'])); // Pass remember_me status
        } else {
            error_log("CRITICAL: createUserSession() function not found. Login will fail to establish session.");
            return false; // Cannot create session
        }

        // Update last login timestamp
        updateRecord('Users', ['last_login' => date('Y-m-d H:i:s')], 'id = ?', [$user['id']]); // Uses updateRecord
        logActivity('login', "User '{$user['username']}' logged in.", $user['id']); // Uses logActivity
        return true;
    }
    logActivity('login_failed', "Failed login attempt for '{$emailOrUsername}'.");
    return false;
}

// --- Input Sanitization & Validation ---

/**
 * Sanitize user input to prevent XSS.
 * @param string $data Input data.
 * @return string Sanitized data.
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(strip_tags(trim((string)$data)), ENT_QUOTES, 'UTF-8');
}
// Alias for consistency if 'sanitize' is used elsewhere
if (!function_exists('sanitize')) {
    function sanitize($data) {
        return sanitizeInput($data);
    }
}


/**
 * Validate email address.
 * @param string $email Email to validate.
 * @return bool True if valid, false otherwise.
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// --- Password Management ---

/**
 * Hash password securely using PHP's password_hash().
 * @param string $password Plain text password.
 * @return string|false Hashed password or false on failure.
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verify password against a hash using PHP's password_verify().
 * @param string $password Plain text password.
 * @param string $hash Hashed password from database.
 * @return bool True if password matches hash, false otherwise.
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// --- Database Interaction Wrappers ---
// These functions rely on $pdo (global from db.php) and executeQuery (from db.php).

/**
 * Fetch a single record from the database.
 * Relies on executeQuery() from db.php.
 * @param string $sql SQL query with placeholders.
 * @param array $params Parameters for the prepared statement.
 * @return array|false Associative array of the record, or false if not found/error.
 */
function getRecord($sql, $params = []) {
    if (!function_exists('executeQuery')) {
        error_log("getRecord error: executeQuery() function not found. Make sure db.php is included and defines it.");
        return false;
    }
    $stmt = executeQuery($sql, $params);
    return $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
}

/**
 * Insert a record into the database.
 * Relies on executeQuery() from db.php and $pdo global from db.php.
 * @param string $table Table name.
 * @param array $data Associative array of data to insert (column => value).
 * @return string|false The ID of the last inserted row, or false on failure.
 */
function insertRecord($table, $data) {
    global $pdo; // From db.php
    if (empty($data) || !$pdo) {
        error_log("insertRecord error: No data provided or PDO connection missing.");
        return false;
    }
    if (!function_exists('executeQuery')) {
        error_log("insertRecord error: executeQuery() function not found.");
        return false;
    }

    $fields = implode(', ', array_keys($data));
    $placeholders = ':' . implode(', :', array_keys($data));
    $sql = "INSERT INTO {$table} ({$fields}) VALUES ({$placeholders})";

    $stmt = executeQuery($sql, $data);
    return $stmt ? $pdo->lastInsertId() : false;
}

/**
 * Update record(s) in the database.
 * Relies on executeQuery() from db.php.
 * @param string $table Table name.
 * @param array $data Associative array of data to update (column => value).
 * @param string $condition SQL WHERE clause (e.g., 'id = ?').
 * @param array $conditionParams Parameters for the WHERE clause.
 * @return int|false Number of affected rows, or false on failure.
 */
function updateRecord($table, $data, $condition, $conditionParams = []) {
    if (empty($data)) {
        error_log("updateRecord error: No data provided for update.");
        return false;
    }
     if (!function_exists('executeQuery')) {
        error_log("updateRecord error: executeQuery() function not found.");
        return false;
    }

    $setClauses = [];
    $paramsForSet = [];
    foreach ($data as $key => $value) {
        $setClauses[] = "{$key} = ?"; // Using positional placeholders for SET part
        $paramsForSet[] = $value;
    }
    $sql = "UPDATE {$table} SET " . implode(', ', $setClauses) . " WHERE {$condition}";

    // Combine params for SET and params for WHERE clause
    $allParams = array_merge($paramsForSet, $conditionParams);

    $stmt = executeQuery($sql, $allParams);
    return $stmt ? $stmt->rowCount() : false;
}

/**
 * Delete record(s) from the database.
 * Relies on executeQuery() from db.php.
 * @param string $table Table name.
 * @param string $condition SQL WHERE clause (e.g., 'id = ?').
 * @param array $params Parameters for the WHERE clause.
 * @return int|false Number of affected rows, or false on failure.
 */
function deleteRecord($table, $condition, $params = []) {
     if (!function_exists('executeQuery')) {
        error_log("deleteRecord error: executeQuery() function not found.");
        return false;
    }
    $sql = "DELETE FROM {$table} WHERE {$condition}";
    $stmt = executeQuery($sql, $params);
    return $stmt ? $stmt->rowCount() : false;
}

// --- Redirection & URL Management ---

/**
 * Redirect to a specified URL.
 * Uses SITE_URL from config.php if defined, for constructing absolute URLs from relative paths.
 * @param string $url The URL or path to redirect to.
 * @param array $params Optional query parameters as an associative array.
 */
function redirect($url, $params = []) {
    if (headers_sent($file, $line)) {
        error_log("Redirect failed: Headers already sent in {$file} on line {$line}. Target URL: {$url}");
        // Output a JS redirect as a fallback, though this is not ideal.
        echo "<script>window.location.href='{$url}';</script>";
        exit;
    }

    $finalUrl = $url;
    // If SITE_URL is defined and $url doesn't look like a full URL, treat it as relative to SITE_URL
    if (defined('SITE_URL') && !preg_match('/^(http:\/\/|https:\/\/|\/\/)/i', $url)) {
        $finalUrl = rtrim(SITE_URL, '/') . '/' . ltrim($url, '/');
    }

    if (!empty($params)) {
        $finalUrl .= (strpos($finalUrl, '?') === false ? '?' : '&') . http_build_query($params);
    }

    header("Location: " . $finalUrl);
    exit;
}

/**
 * Redirects with a message stored in the session.
 * @param string $url URL to redirect to.
 * @param string $message Message to display.
 * @param string $type Type of message ('success', 'error', 'info', 'warning'). Defaults to 'info'.
 */
function redirectWithMessage($url, $message, $type = 'info') {
    if (session_status() === PHP_SESSION_NONE) {
        // This should not happen if auth.php -> auth/session.php runs first
        // Consider starting session here if absolutely necessary, but it's a sign of an include order issue.
        // session_start(); // Or call initSession() from auth/session.php if available
    }
    $_SESSION['flash_message'] = [
        'message' => $message,
        'type' => $type
    ];
    redirect($url);
}

/**
 * Displays and clears a flash message stored in the session.
 * Call this in your header or layout where you want messages to appear.
 * @return string HTML for the message or empty string if no message.
 */
function displayFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $messageData = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']); // Clear the message after displaying

        $message = htmlspecialchars($messageData['message'], ENT_QUOTES, 'UTF-8');
        $type = htmlspecialchars($messageData['type'], ENT_QUOTES, 'UTF-8');

        $alertClass = 'bg-blue-100 border-blue-500 text-blue-700'; // Default info
        if ($type === 'success') {
            $alertClass = 'bg-green-100 border-green-500 text-green-700';
        } elseif ($type === 'error') {
            $alertClass = 'bg-red-100 border-red-500 text-red-700';
        } elseif ($type === 'warning') {
            $alertClass = 'bg-yellow-100 border-yellow-500 text-yellow-700';
        }

        return "<div class=\"border-l-4 p-4 {$alertClass}\" role=\"alert\">
                    <p class=\"font-bold\">" . ucfirst($type) . "</p>
                    <p>{$message}</p>
                </div>";
    }
    return '';
}


// --- Token Generation ---

/**
 * Generates a cryptographically secure random token.
 * Useful for "remember me" cookies, password reset tokens, etc.
 * @param int $length Length of the token in bytes (e.g., 32 bytes = 64 hex characters).
 * @return string Hexadecimal representation of the token.
 */
function generateToken($length = 32) {
    try {
        return bin2hex(random_bytes($length));
    } catch (Exception $e) {
        // Fallback for environments where random_bytes might fail (highly unlikely with modern PHP)
        // This fallback is less secure.
        return bin2hex(openssl_random_pseudo_bytes($length));
    }
}

// --- Activity Logging ---

/**
 * Logs an activity to the ActivityLog table.
 * Relies on insertRecord() from this file and getCurrentUserId().
 * @param string $action_type Type of action (e.g., 'login', 'task_created').
 * @param string $description Detailed description of the activity.
 * @param int|null $user_id User ID associated with the action. If null, tries to get current user.
 * @param string|null $entity_type The type of entity related to the action (e.g., 'User', 'MajorTask', 'Period').
 * @param int|null $entity_id The ID of the entity.
 */
function logActivity($action_type, $description, $user_id = null, $entity_type = null, $entity_id = null) {
    if ($user_id === null) {
        $user_id = getCurrentUserId(); // Get current user if not specified
    }

    $data = [
        'user_id'     => $user_id, // Can be null if action is not user-specific or user is unknown
        'action_type' => sanitizeInput($action_type),
        'entity_type' => $entity_type ? sanitizeInput($entity_type) : null,
        'entity_id'   => $entity_id ? (int)$entity_id : null,
        'description' => sanitizeInput($description),
        // 'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null, // Optional: log IP address
        'created_at'  => date('Y-m-d H:i:s') // Timestamp of the log entry
    ];

    // Assumes you have an 'ActivityLog' table
    // insertRecord must be working correctly.
    $log_id = insertRecord('ActivityLog', $data);
    if (!$log_id) {
        error_log("Failed to log activity: {$action_type} - {$description}");
    }
}

// --- Date and Time Formatting ---
/**
 * Format date for display
 * @param string $date Date string (e.g., from database YYYY-MM-DD or YYYY-MM-DD HH:MM:SS)
 * @param string $format Desired date format (PHP date() function format)
 * @return string Formatted date or a placeholder if date is invalid/empty.
 */
function formatDate($date, $format = 'M d, Y') {
    if (empty($date) || $date === '0000-00-00' || $date === '0000-00-00 00:00:00') {
        return '-'; // Or 'N/A', or empty string
    }
    try {
        $dateTime = new DateTime($date);
        return $dateTime->format($format);
    } catch (Exception $e) {
        // Log error or return a default
        error_log("Error formatting date '{$date}': " . $e->getMessage());
        return '-'; // Or original date string, or 'Invalid Date'
    }
}

// --- UI Helper Functions (Example) ---
/**
 * Get CSS class for task priority badge.
 * @param string $priority Priority level.
 * @return string Tailwind CSS classes.
 */
function getPriorityClass($priority) {
    switch (strtolower($priority ?? '')) {
        case 'critical': return 'bg-red-100 text-red-800';
        case 'high': return 'bg-orange-100 text-orange-800'; // Changed from red-100
        case 'medium': return 'bg-yellow-100 text-yellow-800';
        case 'low': return 'bg-green-100 text-green-800';
        default: return 'bg-gray-100 text-gray-800';
    }
}

/**
 * Get CSS class for task status badge.
 * @param string $status Task status.
 * @return string Tailwind CSS classes.
 */
function getStatusClass($status) {
    switch (strtolower(str_replace(' ', '', $status ?? ''))) {
        case 'completed': return 'bg-green-100 text-green-800';
        case 'inprogress': return 'bg-blue-100 text-blue-800';
        case 'onhold': return 'bg-yellow-100 text-yellow-800'; // Changed from orange
        case 'cancelled': return 'bg-red-100 text-red-800'; // Changed from gray
        case 'todo': default: return 'bg-gray-100 text-gray-800';
    }
}

// Add any other globally useful functions here.

?>
