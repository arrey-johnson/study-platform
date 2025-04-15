<?php
// Start session
session_start();

// Define base path
define('BASE_PATH', dirname(__DIR__));

// Load configuration
require_once BASE_PATH . '/src/Config/config.php';

// Load helper functions
require_once BASE_PATH . '/src/Helpers/functions.php';

// Load database connection
require_once BASE_PATH . '/src/Database/Database.php';

// Get the requested URL
$request_uri = $_SERVER['REQUEST_URI'];
$script_name = $_SERVER['SCRIPT_NAME'];
$base_path = dirname($script_name);

// Remove base path from request URI
$path = substr($request_uri, strlen($base_path));
$path = trim($path, '/');

// Default route
if (empty($path)) {
    $path = 'auth/login';
}

// Split the path into controller and action
$parts = explode('/', $path);
$controller = isset($parts[0]) ? $parts[0] : 'auth';
$action = isset($parts[1]) ? $parts[1] : 'login';

// Load the appropriate controller
$controller_class = ucfirst($controller) . 'Controller';
$controller_file = BASE_PATH . '/src/Controllers/' . $controller_class . '.php';

if (file_exists($controller_file)) {
    require_once $controller_file;
    $controller_class = "App\\Controllers\\" . $controller_class;
    $controller = new $controller_class();
    
    if (method_exists($controller, $action)) {
        $controller->$action();
    } else {
        // Handle 404 - Action not found
        header("HTTP/1.0 404 Not Found");
        echo "404 Not Found";
    }
} else {
    // Handle 404 - Controller not found
    header("HTTP/1.0 404 Not Found");
    echo "404 Not Found";
} 