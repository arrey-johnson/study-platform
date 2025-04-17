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

echo "<h1>Debug Information</h1>";
echo "<h2>Request Details</h2>";
echo "<pre>";
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "\n";
echo "PHP_SELF: " . $_SERVER['PHP_SELF'] . "\n";
echo "APP_URL: " . APP_URL . "\n";
echo "</pre>";

echo "<h2>Controller Loading Test</h2>";
$controller_file = BASE_PATH . '/src/Controllers/AuthController.php';
echo "Controller file path: " . $controller_file . "<br>";
echo "File exists: " . (file_exists($controller_file) ? "Yes" : "No") . "<br>";

if (file_exists($controller_file)) {
    require_once $controller_file;
    $controller_class = "App\\Controllers\\AuthController";
    echo "Controller class: " . $controller_class . "<br>";
    echo "Class exists: " . (class_exists($controller_class) ? "Yes" : "No") . "<br>";
    
    if (class_exists($controller_class)) {
        $controller = new $controller_class();
        echo "Method 'processLogin' exists: " . (method_exists($controller, 'processLogin') ? "Yes" : "No") . "<br>";
    }
}

echo "<h2>Available Controllers</h2>";
$controllers_dir = BASE_PATH . '/src/Controllers/';
$files = scandir($controllers_dir);
echo "<ul>";
foreach ($files as $file) {
    if (substr($file, -4) === '.php') {
        echo "<li>" . $file . "</li>";
    }
}
echo "</ul>";

echo "<h2>Test Form</h2>";
echo '<form action="' . APP_URL . '/auth/processLogin" method="POST">';
echo '<input type="hidden" name="csrf_token" value="test">';
echo '<input type="email" name="email" value="student@example.com">';
echo '<input type="password" name="password" value="password">';
echo '<button type="submit">Test Login</button>';
echo '</form>';
?>
