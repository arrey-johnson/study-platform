<?php
// Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'study_platform');
define('DB_USER', 'root');
define('DB_PASS', '');

// Application configuration
define('APP_NAME', 'Study Platform');
define('APP_URL', 'http://localhost/study-platform/study-platform');
define('APP_PATH', dirname(__DIR__));

// Session configuration
define('SESSION_LIFETIME', 3600 * 24 * 7); // 7 days
define('SESSION_NAME', 'study_platform_session');

// Security configuration
define('CSRF_TOKEN_NAME', 'csrf_token');
define('PASSWORD_HASH_ALGO', PASSWORD_BCRYPT);
define('PASSWORD_HASH_OPTIONS', ['cost' => 12]);

// File upload configuration
define('UPLOAD_PATH', APP_PATH . '/public/uploads');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_FILE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'application/pdf']);

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize session with configured settings
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_lifetime', SESSION_LIFETIME);
    ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
    session_name(SESSION_NAME);
    session_start();
} 