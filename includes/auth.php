<?php
/**
 * Authentication Helper Functions
 * Handles user sessions and authentication checks
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is logged in
 * @return bool True if logged in, false otherwise
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if user is admin
 * @return bool True if admin, false otherwise
 */
function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Get current user ID
 * @return int|null User ID or null if not logged in
 */
function get_user_id() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

/**
 * Get current username
 * @return string|null Username or null if not logged in
 */
function get_username() {
    return isset($_SESSION['username']) ? $_SESSION['username'] : null;
}

/**
 * Get current user email
 * @return string|null Email or null if not logged in
 */
function get_user_email() {
    return isset($_SESSION['email']) ? $_SESSION['email'] : null;
}

/**
 * Require user to be logged in
 * Redirects to login page if not logged in
 */
function require_login() {
    if (!is_logged_in()) {
        header('Location: /movie-viewer/login.php');
        exit();
    }
}

/**
 * Require user to be admin
 * Redirects to login page if not admin
 */
function require_admin() {
    if (!is_admin()) {
        header('Location: /movie-viewer/admin/admin_login.php');
        exit();
    }
}

/**
 * Set user session after successful login
 * @param int $user_id User ID
 * @param string $username Username
 * @param string $email Email
 * @param string $role User role
 */
function set_user_session($user_id, $username, $email, $role) {
    $_SESSION['user_id'] = $user_id;
    $_SESSION['username'] = $username;
    $_SESSION['email'] = $email;
    $_SESSION['role'] = $role;
    
    // Regenerate session ID to prevent session fixation
    session_regenerate_id(true);
}

/**
 * Destroy user session (logout)
 */
function destroy_user_session() {
    // Unset all session variables
    $_SESSION = array();
    
    // Destroy the session cookie
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    // Destroy the session
    session_destroy();
}

/**
 * Generate CSRF token
 * @return string CSRF token
 */
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * @param string $token Token to verify
 * @return bool True if valid, false otherwise
 */
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>
