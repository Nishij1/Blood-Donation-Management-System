CREATE DATABASE blood_donation1;
USE blood_donation1;

CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'donor', 'recipient') NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE donors (
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
);

CREATE TABLE blood_requests (
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
);

CREATE TABLE donations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    donor_id INT,
    request_id INT NULL,
    blood_group ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') NOT NULL,
    units INT DEFAULT 1,
    donation_date DATE,
    status ENUM('scheduled', 'completed', 'cancelled') DEFAULT 'scheduled',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (donor_id) REFERENCES donors(id),
    FOREIGN KEY (request_id) REFERENCES blood_requests(id)
);

CREATE TABLE blood_inventory (
    id INT PRIMARY KEY AUTO_INCREMENT,
    blood_group ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') NOT NULL,
    units INT DEFAULT 0,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE contact_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'replied') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Remove the old admin inserts and add a single, correct admin user
DELETE FROM users WHERE role = 'admin';

-- Add the new admin user with a properly hashed password
INSERT INTO users (name, email, password, role, status, created_at) 
VALUES (
    'Ankit Bodkhe',
    'ankitbodkhe2003@gmail.com',
    '$2y$10$8K1p/a0dR9SD.PJHLJx1U.U7OVV7hXRV5VXPQ1QH1.zIBGZ8yHE8.',  -- Properly hashed 'ankit123'
    'admin',
    'active',
    NOW()
);

-- Initialize blood inventory with all blood groups
INSERT INTO blood_inventory (blood_group, units) VALUES 
('A+', 0), ('A-', 0), ('B+', 0), ('B-', 0),
('AB+', 0), ('AB-', 0), ('O+', 0), ('O-', 0); 