<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $name;
    public $email;
    public $password;
    public $role;
    public $status;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register($name, $email, $password, $role) {
        try {
            $this->conn->beginTransaction();

            // Check if email already exists
            $query = "SELECT id FROM " . $this->table_name . " WHERE email = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() > 0) {
                throw new Exception("Email already exists");
            }

            // Insert into users table
            $query = "INSERT INTO " . $this->table_name . 
                    " (name, email, password, role, status, created_at) VALUES 
                    (:name, :email, :password, :role, 'active', NOW())";
            
            $stmt = $this->conn->prepare($query);
            
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Bind values
            $stmt->bindParam(":name", $name);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":password", $hashed_password);
            $stmt->bindParam(":role", $role);
            
            if (!$stmt->execute()) {
                throw new Exception("Error creating user account");
            }

            $user_id = $this->conn->lastInsertId();

            // Create role-specific record
            if ($role === 'donor') {
                $query = "INSERT INTO donors (user_id) VALUES (:user_id)";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(":user_id", $user_id);
                if (!$stmt->execute()) {
                    throw new Exception("Error creating donor profile");
                }
            } elseif ($role === 'recipient') {
                // Add any recipient-specific initialization if needed
                // For now, we'll just log the registration
                error_log("New recipient registered: " . $user_id);
            }

            $this->conn->commit();
            return true;

        } catch(Exception $e) {
            $this->conn->rollBack();
            error_log("Registration Error: " . $e->getMessage());
            throw new Exception("Registration failed: " . $e->getMessage());
        }
    }

    public function login($email, $password) {
        $query = "SELECT id, name, email, password, role, status FROM " . $this->table_name . 
                " WHERE email = :email LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch();
            
            if ($row['status'] !== 'active') {
                throw new Exception("Account is not active. Please contact administrator.");
            }

            if(password_verify($password, $row['password'])) {
                if (!isset($_SESSION)) {
                    session_start();
                }
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_role'] = $row['role'];
                $_SESSION['user_name'] = $row['name'];
                $_SESSION['last_activity'] = time();
                
                // Log the login activity
                $this->logActivity($row['id'], 'User logged in');
                
                return true;
            }
        }
        return false;
    }

    private function logActivity($user_id, $action) {
        $query = "INSERT INTO activity_logs (user_id, action, ip_address) 
                 VALUES (:user_id, :action, :ip)";
        $stmt = $this->conn->prepare($query);
        $ip = $_SERVER['REMOTE_ADDR'];
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":action", $action);
        $stmt->bindParam(":ip", $ip);
        return $stmt->execute();
    }

    public function updateProfile($id, $data) {
        try {
            $allowed_fields = ['name', 'email', 'phone', 'address'];
            $updates = [];
            $params = [];

            foreach ($data as $key => $value) {
                if (in_array($key, $allowed_fields)) {
                    $updates[] = "$key = :$key";
                    $params[":$key"] = $value;
                }
            }

            if (empty($updates)) {
                return false;
            }

            $query = "UPDATE " . $this->table_name . " SET " . 
                    implode(", ", $updates) . 
                    " WHERE id = :id";
            
            $params[":id"] = $id;
            
            $stmt = $this->conn->prepare($query);
            return $stmt->execute($params);
        } catch(PDOException $e) {
            throw $e;
        }
    }

    public function changePassword($id, $current_password, $new_password) {
        try {
            // Verify current password
            $query = "SELECT password FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            
            if ($row = $stmt->fetch()) {
                if (!password_verify($current_password, $row['password'])) {
                    throw new Exception("Current password is incorrect");
                }
            } else {
                throw new Exception("User not found");
            }

            // Update password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $query = "UPDATE " . $this->table_name . 
                    " SET password = :password WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":password", $hashed_password);
            $stmt->bindParam(":id", $id);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            throw $e;
        }
    }

    public function isAdmin() {
        return $this->role === 'admin';
    }
}
?> 