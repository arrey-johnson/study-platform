<?php
namespace App\Controllers;

use App\Database\Database;

class Controller
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Render a view with layout
     */
    protected function render($view, $data = [])
    {
        // Extract data to make variables available in view
        extract($data);

        // Start output buffering
        ob_start();

        // Include the view file
        require_once __DIR__ . '/../Views/' . $view . '.php';

        // Get the contents of the buffer
        $content = ob_get_clean();

        // Include the layout
        require_once __DIR__ . '/../Views/layouts/main.php';
    }

    /**
     * Redirect to a URL
     */
    protected function redirect($path)
    {
        header('Location: ' . APP_URL . $path);
        exit;
    }

    /**
     * Check if request is POST
     */
    protected function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Get POST data
     */
    protected function getPostData()
    {
        return $_POST;
    }

    /**
     * Require user to be logged in
     */
    protected function requireLogin()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/auth/login');
        }
    }

    /**
     * Require user to be admin
     */
    protected function requireAdmin()
    {
        $this->requireLogin();
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            $this->redirect('/auth/login');
        }
    }

    /**
     * Require user to be student
     */
    protected function requireStudent()
    {
        $this->requireLogin();
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
            $this->redirect('/auth/login');
        }
    }
} 