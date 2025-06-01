<?php
/**
 * Session Management Functions
 * * Handles session initialization and security
 * * @author Dr. Ahmed AL-sadi
 * @version 1.1 - Revised for robustness
 */

// Ensure ROOT_PATH_FOR_SESSION is defined.
// This makes session.php more portable if included from different locations.
if (!defined('ROOT_PATH_FOR_SESSION')) {
    // Assumes session.php is in a subdirectory (like /auth/) of the main application root.
    define('ROOT_PATH_FOR_SESSION', dirname(__DIR__)); // This will be /home/gotoa957/workhub.gotoaus.com
}

// --- Include Core Files ---
// The order of these includes can be important.

// 1. Config: Defines essential constants.
$configFile = ROOT_PATH_FOR_SESSION . '/includes/config.php';
if (file_exists($configFile)) {
    require_once $configFile;
} else {
    error_log("CRITICAL ERROR in auth/session.php: config.php not found at " . $configFile);
    // For development, you might die here to see the problem immediately.
    // die("Critical Error: Main configuration file (config.php) not found. Path checked: " . $configFile);
    // In production, just logging might be preferred, but the app will likely fail.
    // If config.php is missing, many things including DB connection will fail.
}

// 2. Database Functions: Needed by functions.php (for logActivity) and session functions here.
//    db.php should itself include config.php if it needs DB constants (which it does).
$dbFile = ROOT_PATH_FOR_SESSION . '/includes/db.php';
if (file_exists($dbFile)) {
    require_once $dbFile;
} else {
    error_log("CRITICAL ERROR in auth/session.php: db.php not found at " . $dbFile);
    // die("Critical Error: Database functions file (db.php) not found. Path checked: " . $dbFile);
}

// 3. General Functions: Defines isLoggedIn(), logActivity(), hashPassword(), etc.
//    logActivity() in functions.php uses insertRecord() from db.php, so db.php should be loaded first.
$functionsFile = ROOT_PATH_FOR_SESSION . '/includes/functions.php';
if (file_exists($functionsFile)) {
    require_once $functionsFile;
    // echo "DEBUG: functions.php was included by session.php.<br>"; // Optional: temporary echo
} else {
    // This part will now stop everything if the file isn't found by file_exists()
    die("FATAL ERROR in auth/session.php: functions.php NOT FOUND at path: " . htmlspecialchars($functionsFile) . ". Please check the path and file existence.");
}

/**
 * Initialize session with secure settings
 */
function initSession() {
    // Only attempt to set cookie parameters if no session has been started by PHP yet.
    // This directly addresses the "Session cookie parameters cannot be changed" warning.
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'lifetime' => defined('SESSION_LIFETIME') ? intval(SESSION_LIFETIME) : 3600,
            'path'     => '/',
            'domain'   => defined('SESSION_COOKIE_DOMAIN') ? SESSION_COOKIE_DOMAIN : '', // Define in config.php if needed (e.g., '.yourdomain.com')
            'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off', // Stricter check for HTTPS
            'httponly' => true,
            'samesite' => defined('SESSION_SAMESITE') ? SESSION_SAMESITE : 'Lax'    // Define in config.php ('Lax' or 'Strict')
        ]);
        session_start(); // Start the session *after* setting params.
    }
    // If session is already active (PHP_SESSION_ACTIVE), we can't change params.
    // The warning means this was likely the case, possibly due to PHP's session.auto_start.
    // It's best if session.auto_start is Off in php.ini and this script controls session start.

    // Regenerate session ID periodically to help prevent session fixation.
    // Ensure session has been started and $_SESSION is available.
    if (session_status() === PHP_SESSION_ACTIVE) {
        if (!isset($_SESSION['created'])) {
            $_SESSION['created'] = time();
        } elseif (time() - $_SESSION['created'] > (defined('SESSION_REGENERATE_TIME') ? intval(SESSION_REGENERATE_TIME) : 1800)) { // e.g., 30 minutes
            if (!headers_sent()) { // Check if headers already sent before regenerating
                session_regenerate_id(true); // Regenerate ID and delete old session file.
            }
            $_SESSION['created'] = time(); // Reset creation time.
        }
    }
}

/**
 * Create a new user session
 * (Assumes this function and others like checkRememberMeCookie, destroyUserSession are defined below)
 * This function uses logActivity (from functions.php) which uses insertRecord (from db.php).
 * It also uses updateRecord (from db.php).
 */
function createUserSession($user, $remember = false) {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        // This shouldn't happen if initSession() is called, but as a safeguard.
        initSession();
    }

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['last_login_timestamp'] = time();

    if (function_exists('updateRecord')) {
        updateRecord('Users',
            ['last_login' => date('Y-m-d H:i:s')],
            'id = ?',
            [$user['id']]
        );
    }

    if ($remember) {
        if (function_exists('generateToken') && function_exists('hashPassword') && function_exists('insertRecord')) {
            $token = generateToken(64);
            $tokenHash = hashPassword($token); // Uses hashPassword from functions.php

            $expiresAt = time() + (defined('COOKIE_LIFETIME') ? intval(COOKIE_LIFETIME) : 604800); // 7 days
            insertRecord('UserSessions', [
                'user_id' => $user['id'],
                'session_token' => $tokenHash,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
                'expires_at' => date('Y-m-d H:i:s', $expiresAt)
            ]);

            // Set the actual cookie
            if (!headers_sent()) {
                 setcookie('remember_token', $user['id'] . ':' . $token, [
                    'expires' => $expiresAt,
                    'path' => '/',
                    'domain' => defined('SESSION_COOKIE_DOMAIN') ? SESSION_COOKIE_DOMAIN : '',
                    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
                    'httponly' => true,
                    'samesite' => defined('SESSION_SAMESITE') ? SESSION_SAMESITE : 'Lax'
                ]);
            }
        }
    }

    if (function_exists('logActivity')) {
        logActivity('login', 'User logged in', $user['id']); // Uses logActivity from functions.php
    }
}

/**
 * Check for remember me cookie and log user in
 * This uses getRecord (from db.php) and verifyPassword (from functions.php)
 */
function checkRememberMeCookie() {
    if (isset($_COOKIE['remember_token']) && function_exists('isLoggedIn') && !isLoggedIn()) {
        if (!function_exists('getRecord') || !function_exists('verifyPassword')) {
            error_log("checkRememberMeCookie: getRecord or verifyPassword not available.");
            return false;
        }

        list($userId, $token) = explode(':', $_COOKIE['remember_token'], 2);

        if (empty($userId) || empty($token)) return false;
        $userId = (int)$userId;

        $sql = "SELECT us.session_token, u.* FROM UserSessions us
                JOIN Users u ON u.id = us.user_id
                WHERE us.user_id = ? AND us.expires_at > ? AND u.is_active = TRUE
                ORDER BY us.created_at DESC LIMIT 1";

        $sessionRecord = getRecord($sql, [$userId, date('Y-m-d H:i:s')]);

        if ($sessionRecord && verifyPassword($token, $sessionRecord['session_token'])) {
            // Valid token, log user in
            // For simplicity, creating the session here. A more robust solution might
            // invalidate the current remember_token and issue a new one.
            // The original $user array for createUserSession should come from $sessionRecord
            // which already contains joined User data.
            $userDataFromSessionRecord = [];
            foreach ($sessionRecord as $key => $value) {
                // Assuming UserSessions columns don't conflict badly with Users table column names after join
                // or that Users.* takes precedence.
                if ($key !== 'session_token') { // Exclude session_token itself from user data
                    $userDataFromSessionRecord[$key] = $value;
                }
            }
            createUserSession($userDataFromSessionRecord, true); // Re-issue remember me to extend or refresh
            return true;
        }

        // Invalid or expired token, clear cookie and DB entry if appropriate
        if ($userId > 0 && function_exists('deleteRecord') && function_exists('hashPassword')) {
             // To delete the specific token, you'd need to re-hash the cookie token
             // if you only store hashed tokens.
             // For simplicity, if token invalid, just clear cookie.
             // deleteRecord('UserSessions', 'user_id = ? AND session_token = ?', [$userId, hashPassword($token)]);
        }
        if (!headers_sent()) {
            setcookie('remember_token', '', time() - 3600, '/'); // Clear cookie
        }
    }
    return false;
}

/**
 * End user session and clear cookies
 * This uses logActivity (from functions.php) and deleteRecord (from db.php)
 */
function destroyUserSession() {
    if (function_exists('isLoggedIn') && isLoggedIn() && function_exists('logActivity')) {
        logActivity('logout', 'User logged out', (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null) );
    }

    if (isset($_COOKIE['remember_token'])) {
        if (function_exists('deleteRecord')) {
            list($userId, $token) = explode(':', $_COOKIE['remember_token'], 2);
            if (!empty($userId)) {
                $userId = (int) $userId;
                 // Delete all session tokens for the user for simplicity upon manual logout.
                 // Or you can try to delete the specific one if $token is available and you stored it unhashed
                 // or can find it by re-hashing and comparing.
                deleteRecord('UserSessions', 'user_id = ?', [$userId]);
            }
        }
        if (!headers_sent()) {
            setcookie('remember_token', '', time() - 3600, '/'); // Clear cookie
        }
    }

    $_SESSION = []; // Clear all session variables

    if (ini_get("session.use_cookies") && !headers_sent()) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    if (session_status() === PHP_SESSION_ACTIVE && !headers_sent()) {
        session_destroy(); // Destroy the session data on the server.
    }
}


// --- Initialize Session ---
// This function call actually starts the session if not already started.
initSession();


// --- Final Checks after session is initialized ---
// Check for remember me cookie only if isLoggedIn() is available and user is not logged in.
if (function_exists('isLoggedIn')) {
    if (!isLoggedIn()) { // This was the line causing "undefined function isLoggedIn"
        if (function_exists('checkRememberMeCookie')) {
            checkRememberMeCookie();
        } else {
            error_log("Error in auth/session.php: checkRememberMeCookie() function not found.");
        }
    }
} else {
    // This means functions.php (where isLoggedIn is defined) was not included correctly.
    // The error logging at the top for file inclusion should catch this.
    error_log("FATAL ERROR in auth/session.php: isLoggedIn() function not found. functions.php likely failed to load.");
    // For development, to make it obvious:
    // die("FATAL ERROR in auth/session.php: isLoggedIn() function not found. Check include paths and errors in functions.php. Error logged.");
}

?>