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

// Convert hyphens to camelCase (process-login becomes processLogin)
if (strpos($action, '-') !== false) {
    $action_parts = explode('-', $action);
    $action = $action_parts[0];
    for ($i = 1; $i < count($action_parts); $i++) {
        $action .= ucfirst($action_parts[$i]);
    }
}

// Load the appropriate controller
$controller_class = ucfirst($controller) . 'Controller';
$controller_file = BASE_PATH . '/src/Controllers/' . $controller_class . '.php';

if (file_exists($controller_file)) {
    require_once $controller_file;
    $controller_class = "App\\Controllers\\" . $controller_class;
    $controller = new $controller_class();
    
    // Convert action to camelCase for method name
    $action_method = lcfirst($action);
    
    // Check if method exists (case sensitive)
    if (method_exists($controller, $action_method)) {
        $controller->$action_method();
    } else {
        // Try to find a case-insensitive match
        $methods = get_class_methods($controller);
        $action_lower = strtolower($action_method);
        $method_found = false;
        
        foreach ($methods as $method) {
            if (strtolower($method) === $action_lower) {
                $controller->$method();
                $method_found = true;
                break;
            }
        }
        
        if (!$method_found) {
            // Handle 404 - Action not found
            header("HTTP/1.0 404 Not Found");
            echo "404 Not Found - Action '$action' does not exist in controller '$controller_class'";
        }
    }
} else {
    // Handle 404 - Controller not found
    header("HTTP/1.0 404 Not Found");
    echo "404 Not Found - Controller '$controller_class' does not exist";
}