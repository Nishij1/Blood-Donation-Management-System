<?php
class BaseController {
    protected $db;
    protected $user;

    public function __construct() {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check session timeout
        if (isset($_SESSION['last_activity']) && 
            (time() - $_SESSION['last_activity'] > SESSION_LIFETIME)) {
            $this->logout();
        }
        $_SESSION['last_activity'] = time();

        // Initialize database connection
        $database = new Database();
        $this->db = $database->getConnection();
    }

    protected function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    protected function requireLogin() {
        if (!$this->isLoggedIn()) {
            header("Location: " . BASE_URL . "/login.php");
            exit();
        }
    }

    protected function requireRole($roles) {
        $this->requireLogin();
        
        if (!is_array($roles)) {
            $roles = [$roles];
        }
        
        if (!in_array($_SESSION['user_role'], $roles)) {
            header("Location: " . BASE_URL . "/unauthorized.php");
            exit();
        }
    }

    protected function logout() {
        session_destroy();
        header("Location: " . BASE_URL . "/login.php");
        exit();
    }

    protected function sanitizeInput($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->sanitizeInput($value);
            }
        } else {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
        }
        return $data;
    }

    protected function jsonResponse($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
}
?> 