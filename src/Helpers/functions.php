<?php
/**
 * Escape HTML special characters
 */
function e($string)
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Generate asset URL
 */
function asset($path)
{
    return APP_URL . '/assets/' . ltrim($path, '/');
}

/**
 * Generate CSRF token
 */
function csrf_token()
{
    if (empty($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

/**
 * Verify CSRF token
 */
function verify_csrf_token($token)
{
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

/**
 * Set flash message
 */
function set_flash($type, $message)
{
    $_SESSION['flash_type'] = $type;
    $_SESSION['flash_message'] = $message;
}

/**
 * Check if user is logged in
 */
function is_logged_in()
{
    return isset($_SESSION['user_id']);
}

/**
 * Check if user is admin
 */
function is_admin()
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Check if user is student
 */
function is_student()
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'student';
}

/**
 * Redirect to URL
 */
function redirect($path)
{
    header('Location: ' . APP_URL . '/' . ltrim($path, '/'));
    exit;
}

/**
 * Format date
 */
function format_date($date, $format = 'Y-m-d')
{
    return date($format, strtotime($date));
}

/**
 * Truncate string
 */
function str_truncate($string, $length = 100)
{
    if (strlen($string) <= $length) {
        return $string;
    }
    return substr($string, 0, $length) . '...';
} 