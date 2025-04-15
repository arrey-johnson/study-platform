<?php
namespace App\Controllers;

class AuthController extends Controller
{
    /**
     * Show login page
     */
    public function login()
    {
        // If user is already logged in, redirect to appropriate dashboard
        if (isset($_SESSION['user_id'])) {
            if ($_SESSION['role'] === 'admin') {
                $this->redirect('/admin/dashboard');
            } else {
                $this->redirect('/student/dashboard');
            }
        }
        
        $this->render('auth/login');
    }

    /**
     * Process login form
     */
    public function processLogin()
    {
        if (!$this->isPost()) {
            $this->redirect('/auth/login');
        }

        // Verify CSRF token
        if (!verify_csrf_token($_POST[CSRF_TOKEN_NAME] ?? '')) {
            set_flash('error', 'Invalid request');
            $this->redirect('/auth/login');
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        // Validate input
        if (empty($email) || empty($password)) {
            set_flash('error', 'Please fill in all fields');
            $this->redirect('/auth/login');
        }

        // Get user from database
        $user = $this->db->fetch(
            "SELECT * FROM users WHERE email = ?",
            [$email]
        );

        // Verify user exists and password is correct
        if (!$user || !password_verify($password, $user['password'])) {
            set_flash('error', 'Invalid email or password');
            $this->redirect('/auth/login');
        }

        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];

        // Redirect to appropriate dashboard
        if ($user['role'] === 'admin') {
            $this->redirect('/admin/dashboard');
        } else {
            $this->redirect('/student/dashboard');
        }
    }

    /**
     * Logout user
     */
    public function logout()
    {
        session_destroy();
        $this->redirect('/auth/login');
    }
} 