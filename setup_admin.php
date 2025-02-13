<?php
require_once 'config/config.php';
require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Check if admin already exists
    $stmt = $db->prepare("SELECT id FROM users WHERE email = 'admin@gmail.com'");
    $stmt->execute();
    
    if ($stmt->rowCount() == 0) {
        // Create admin user
        $query = "INSERT INTO users (name, email, password, role, status, created_at) 
                  VALUES (:name, :email, :password, 'admin', 'active', NOW())";
        
        $stmt = $db->prepare($query);
        
        $name = "Admin";
        $email = "admin@gmail.com";
        $password = password_hash("admin123", PASSWORD_DEFAULT);
        
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password", $password);
        
        if ($stmt->execute()) {
            echo "<div style='text-align:center; margin-top:50px;'>";
            echo "<h2>Admin Account Created Successfully!</h2>";
            echo "<p>Email: admin@gmail.com</p>";
            echo "<p>Password: admin123</p>";
            echo "<p><a href='login.php' style='text-decoration:none;'>Go to Login</a></p>";
            echo "</div>";
        } else {
            echo "Error creating admin account.";
        }
    } else {
        echo "<div style='text-align:center; margin-top:50px;'>";
        echo "<h2>Admin account already exists</h2>";
        echo "<p><a href='login.php' style='text-decoration:none;'>Go to Login</a></p>";
        echo "</div>";
    }
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Setup Admin - Blood Donation System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
</body>
</html> 