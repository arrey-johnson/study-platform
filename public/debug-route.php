<?php
// Display all server variables to debug routing
echo "<h1>Route Debugging Information</h1>";

echo "<h2>Server Variables</h2>";
echo "<pre>";
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "\n";
echo "PHP_SELF: " . $_SERVER['PHP_SELF'] . "\n";
echo "DOCUMENT_ROOT: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "</pre>";

echo "<h2>URL Path Analysis</h2>";
$request_uri = $_SERVER['REQUEST_URI'];
$script_name = $_SERVER['SCRIPT_NAME'];
$base_path = dirname($script_name);

echo "<pre>";
echo "Base Path: " . $base_path . "\n";
echo "Path after Base: " . substr($request_uri, strlen($base_path)) . "\n";
echo "</pre>";

echo "<h2>Test Links</h2>";
echo "<ul>";
echo "<li><a href='/study-platform/study-platform/debug-route.php?test=1'>Test with query parameter</a></li>";
echo "<li><a href='/study-platform/study-platform/auth/login'>Auth Login</a></li>";
echo "<li><a href='/study-platform/study-platform/student/dashboard'>Student Dashboard</a></li>";
echo "</ul>";

// Check if htaccess is working
echo "<h2>Htaccess Test</h2>";
echo "If .htaccess rewrite is working correctly, accessing a non-existent file should be handled by index.php<br>";
echo "Try: <a href='/study-platform/study-platform/this-file-does-not-exist.php'>Test non-existent file</a>";

// Check if direct PHP file access works
echo "<h2>Direct File Access Test</h2>";
echo "<a href='/study-platform/study-platform/simple-login.php'>Access simple-login.php directly</a>";

// Display current session info
echo "<h2>Current Session</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Display all defined constants
echo "<h2>Defined Constants</h2>";
echo "<pre>";
$constants = get_defined_constants(true);
if (isset($constants['user'])) {
    print_r($constants['user']);
}
echo "</pre>";
?>
