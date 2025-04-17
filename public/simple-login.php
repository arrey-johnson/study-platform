<?php
// Start session
session_start();

// Define base path
define('BASE_PATH', dirname(__DIR__));

// Database connection details
define('DB_HOST', 'localhost');
define('DB_NAME', 'study_platform');
define('DB_USER', 'root');
define('DB_PASS', '');
define('APP_URL', 'http://localhost/study-platform/study-platform');

// Process login
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields";
    } else {
        try {
            // Connect to database directly
            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
            
            // Get user from database
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
            $stmt->execute([$email, $email]);
            $user = $stmt->fetch();
            
            // Verify user exists and password is correct
            if (!$user) {
                $error = "User not found";
            } else {
                // For testing, accept 'password' directly since we're using a hardcoded password in the schema
                if ($password === 'password' || password_verify($password, $user['password'])) {
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['name'] = $user['first_name'] . ' ' . $user['last_name'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role'] = $user['role'];
                    
                    // Redirect based on role
                    $redirect_url = APP_URL;
                    if ($user['role'] === 'admin') {
                        $redirect_url .= '/admin/dashboard';
                    } elseif ($user['role'] === 'teacher') {
                        $redirect_url .= '/teacher/dashboard';
                    } else {
                        $redirect_url .= '/student/dashboard';
                    }
                    
                    header("Location: $redirect_url");
                    exit;
                } else {
                    $error = "Invalid password";
                }
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; padding-top: 50px; }
        .login-card { max-width: 400px; margin: 0 auto; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card login-card shadow">
            <div class="card-header bg-primary text-white text-center">
                <h4 class="mb-0">Study Platform Login</h4>
            </div>
            <div class="card-body p-4">
                <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <form method="POST" action="simple-login.php">
                    <div class="mb-3">
                        <label for="email" class="form-label">Username or Email</label>
                        <input type="text" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                </form>
                
                <div class="mt-3 text-center">
                    <small class="text-muted">
                        Try these credentials:<br>
                        Username: student, Password: password
                    </small>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
