<?php
/**
 * MyWorkHub - Authentication Setup (includes/auth.php)
 *
 * This file should be included at the beginning of any page
 * that requires user authentication or session management.
 *
 * It ensures that:
 * 1. Configuration is loaded.
 * 2. Database connection functions are available.
 * 3. Core utility and authentication helper functions are available.
 * 4. Session is initialized and managed.
 *
 * IMPORTANT:
 * This file relies on:
 * - `includes/config.php`: For site and database configuration.
 * CRITICAL: Remove `session_start()` from config.php if it exists there,
 * as auth/session.php handles session initialization more robustly.
 * - `includes/db.php`: For database interaction functions.
 * - `includes/functions.php`: For general utility functions (e.g., sanitize, redirect) AND
 * CRITICAL authentication helper functions (see list in file header).
 * These functions MUST be correctly defined in functions.php.
 * - `auth/session.php`: For detailed session mechanics (initSession, createUserSession,
 * destroyUserSession, checkRememberMeCookie).
 *
 * @author Dr. Ahmed AL-sadi (Modified by AI for clarity)
 * @version 1.2
 */

// Define ROOT_PATH if it's not already defined by an entry script (e.g., index.php in workhub/)
// This assumes auth.php is in the 'includes' directory, so 'workhub' is one level up.
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__)); // Resolves to the 'workhub' directory
}

// 1. Load Configuration
// NOTE: Ensure config.php does NOT call session_start() itself.
// auth/session.php (included later) will handle session_set_cookie_params and session_start.
require_once ROOT_PATH . '/includes/config.php';

// 2. Load Database Connection Utilities
// db.php includes config.php if DB_HOST is not defined, require_once handles this.
require_once ROOT_PATH . '/includes/db.php';

// 3. Load Core Functions (including authentication helpers)
// This file MUST define isLoggedIn(), getCurrentUserId(), loginUser(), etc.
// These functions are used by auth/session.php and other parts of the application.
require_once ROOT_PATH . '/includes/functions.php';

// 4. Load and Initialize Session Management
// auth/session.php handles initSession(), createUserSession(), destroyUserSession(),
// checkRememberMeCookie() and calls initSession() itself.
// It also includes config.php, db.php, and functions.php again, but require_once handles redundancy.
require_once ROOT_PATH . '/auth/session.php';

// At this point:
// - Configuration is loaded.
// - Database functions are available.
// - Utility and authentication helper functions (from functions.php) are available.
// - Session has been initialized by auth/session.php.
// - Remember-me cookie (if any) has been checked by auth/session.php.

/**
 * Ensures that a user is logged in. If not, redirects to the login page.
 * Call this at the top of pages that require authentication.
 */
function require_login() {
    // isLoggedIn() must be defined in functions.php
    if (!function_exists('isLoggedIn') || !isLoggedIn()) {
        // redirect() or redirectWithMessage() must be defined in functions.php
        // These functions should handle constructing the full URL if SITE_URL is defined.
        if (function_exists('redirectWithMessage')) {
             redirectWithMessage('login.php', 'Please log in to access this page.', 'error');
        } elseif(function_exists('redirect')) {
            redirect('login.php'); // Assumes redirect handles relative path from site root
        } else {
            // Fallback if redirect functions are missing (basic redirect)
            // Ensure SITE_URL is defined in config.php and ends with a slash if needed, or adjust path.
            $login_url = (defined('SITE_URL') ? rtrim(SITE_URL, '/') : '') . '/login.php';
            header('Location: ' . $login_url);
            exit;
        }
        exit; // Ensure script execution stops after redirect header
    }
}

/**
 * Ensures that a user has a specific role or one of a set of roles.
 * If not, redirects or shows an error.
 *
 * @param mixed $requiredRole A string for a single role, or an array of allowed roles.
 */
function require_role($requiredRole) {
    // isLoggedIn() and hasRole() must be defined in functions.php
    if (!function_exists('isLoggedIn') || !isLoggedIn()) {
        require_login(); // Handles redirect if not logged in
        exit;
    }
    if (!function_exists('hasRole') || !hasRole($requiredRole)) {
        // Handle unauthorized access, e.g., redirect to dashboard with an error message
        if (function_exists('redirectWithMessage')) {
            redirectWithMessage('dashboard.php', 'You do not have permission to access this page.', 'error');
        } elseif(function_exists('redirect')) {
            redirect('dashboard.php?err=unauthorized'); // Assumes redirect handles relative path
        } else {
            // Fallback
            $dashboard_url = (defined('SITE_URL') ? rtrim(SITE_URL, '/') : '') . '/dashboard.php?err=unauthorized';
            header('Location: ' . $dashboard_url);
            exit;
        }
        exit; // Ensure script execution stops
    }
}

?>
