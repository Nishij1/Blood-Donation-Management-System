<?php
require_once 'config/config.php';

try {
    // Create connection without database
    $conn = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create database
    $sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
    $conn->exec($sql);
    echo "Database created successfully<br>";

    // Select the database
    $conn->exec("USE " . DB_NAME);

    // Create users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin', 'donor', 'recipient') NOT NULL,
        status ENUM('active', 'inactive', 'pending') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->exec($sql);
    echo "Users table created successfully<br>";

    // Create donors table
    $sql = "CREATE TABLE IF NOT EXISTS donors (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT,
        blood_group ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') NULL,
        age INT NULL,
        weight FLOAT NULL,
        last_donation_date DATE NULL,
        contact_number VARCHAR(15) NULL,
        address TEXT NULL,
        medical_conditions TEXT NULL,
        availability BOOLEAN DEFAULT true,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )";
    $conn->exec($sql);
    echo "Donors table created successfully<br>";

    // Create blood_requests table
    $sql = "CREATE TABLE IF NOT EXISTS blood_requests (
        id INT PRIMARY KEY AUTO_INCREMENT,
        recipient_id INT,
        blood_group ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') NOT NULL,
        units_needed INT NOT NULL,
        urgency_level ENUM('normal', 'urgent', 'critical') DEFAULT 'normal',
        hospital_name VARCHAR(100),
        hospital_address TEXT,
        contact_person VARCHAR(100),
        contact_number VARCHAR(15),
        status ENUM('pending', 'approved', 'completed', 'cancelled') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (recipient_id) REFERENCES users(id)
    )";
    $conn->exec($sql);
    echo "Blood requests table created successfully<br>";

    // Create blood_inventory table
    $sql = "CREATE TABLE IF NOT EXISTS blood_inventory (
        id INT PRIMARY KEY AUTO_INCREMENT,
        blood_group ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') NOT NULL,
        units INT DEFAULT 0,
        last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $conn->exec($sql);
    echo "Blood inventory table created successfully<br>";

    // Create contact_messages table
    $sql = "CREATE TABLE IF NOT EXISTS contact_messages (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        message TEXT NOT NULL,
        status ENUM('new', 'read', 'replied') DEFAULT 'new',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->exec($sql);
    echo "Contact messages table created successfully<br>";

    // Create default admin user
    $sql = "INSERT INTO users (name, email, password, role) 
            SELECT 'Admin', 'admin@example.com', ?, 'admin'
            WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'admin@example.com')";
    $stmt = $conn->prepare($sql);
    $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt->execute([$admin_password]);
    echo "Default admin user created successfully<br>";

    echo "Database setup completed successfully!";

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 