<?php
require_once '../config/config.php';
require_once '../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // First, check if tables exist
    $db->query("DESCRIBE users");
    
    // Clear any existing admin accounts
    $db->query("DELETE FROM users WHERE role = 'admin'");
    
    // Create new admin account with proper password hashing
    $password = 'ankit123';
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $query = "INSERT INTO users (name, email, password, role, status, created_at) 
              VALUES (:name, :email, :password, 'admin', 'active', NOW())";
    
    $stmt = $db->prepare($query);
    $result = $stmt->execute([
        ':name' => 'Ankit Bodkhe',
        ':email' => 'ankitbodkhe2003@gmail.com',
        ':password' => $hashed_password
    ]);
    
    if ($result) {
        echo "<div style='color: green; margin: 20px;'>";
        echo "Admin account created successfully!<br>";
        echo "Email: ankitbodkhe2003@gmail.com<br>";
        echo "Password: ankit123<br>";
        echo "Hash: " . $hashed_password . "<br>";
        echo "Please delete this file after use.</div>";
    } else {
        echo "<div style='color: red; margin: 20px;'>Failed to create admin account.</div>";
    }
    
} catch (Exception $e) {
    echo "<div style='color: red; margin: 20px;'>";
    echo "Error: " . $e->getMessage() . "<br>";
    echo "Make sure your database is properly configured and the users table exists.";
    echo "</div>";
}
?> 