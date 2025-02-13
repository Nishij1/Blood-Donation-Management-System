<?php
require_once '../config/config.php';
require_once '../config/database.php';

// Indian names arrays
$first_names = [
    'Aarav', 'Arjun', 'Advait', 'Bharat', 'Chirag', 'Dev', 'Dhruv', 'Eshan',
    'Farhan', 'Gaurav', 'Harsh', 'Ishaan', 'Kabir', 'Krishna', 'Lakshay',
    'Aanya', 'Diya', 'Gauri', 'Ishita', 'Kavya', 'Lakshmi', 'Mira', 'Neha',
    'Priya', 'Riya', 'Saanvi', 'Tanvi', 'Uma', 'Vanya', 'Zara'
];

$last_names = [
    'Patel', 'Kumar', 'Singh', 'Shah', 'Sharma', 'Verma', 'Gupta', 'Malhotra',
    'Kapoor', 'Joshi', 'Chauhan', 'Yadav', 'Tiwari', 'Mishra', 'Reddy'
];

// Indian cities
$cities = [
    'Mumbai', 'Delhi', 'Bangalore', 'Pune', 'Chennai', 'Hyderabad', 'Kolkata',
    'Ahmedabad', 'Nagpur', 'Indore', 'Thane', 'Bhopal', 'Visakhapatnam', 'Surat'
];

// Blood groups
$blood_groups = ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'];

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Start transaction
    $db->beginTransaction();
    
    // Generate 30 random users
    for ($i = 0; $i < 30; $i++) {
        $first_name = $first_names[array_rand($first_names)];
        $last_name = $last_names[array_rand($last_names)];
        $city = $cities[array_rand($cities)];
        $blood_group = $blood_groups[array_rand($blood_groups)];
        
        // Generate random age between 18 and 60
        $age = rand(18, 60);
        
        // Generate random weight between 50 and 100
        $weight = rand(50, 100);
        
        // Generate random phone number
        $phone = '91' . rand(7000000000, 9999999999);
        
        // Create user
        $user_query = "INSERT INTO users (name, email, password, role, status, created_at) 
                      VALUES (:name, :email, :password, :role, 'active', NOW())";
        
        $user_stmt = $db->prepare($user_query);
        $user_stmt->execute([
            ':name' => $first_name . ' ' . $last_name,
            ':email' => strtolower($first_name . '.' . $last_name . rand(100, 999) . '@gmail.com'),
            ':password' => password_hash('user123', PASSWORD_DEFAULT),
            ':role' => rand(0, 1) ? 'donor' : 'recipient'
        ]);
        
        $user_id = $db->lastInsertId();
        
        // If user is a donor, create donor record
        if ($user_stmt->rowCount() > 0) {
            $donor_query = "INSERT INTO donors (user_id, blood_group, age, weight, contact_number, address, 
                           last_donation_date, medical_conditions, availability) 
                           VALUES (:user_id, :blood_group, :age, :weight, :contact_number, :address, 
                           :last_donation_date, :medical_conditions, :availability)";
            
            $donor_stmt = $db->prepare($donor_query);
            $donor_stmt->execute([
                ':user_id' => $user_id,
                ':blood_group' => $blood_group,
                ':age' => $age,
                ':weight' => $weight,
                ':contact_number' => $phone,
                ':address' => "Sample Address, " . $city . ", India",
                ':last_donation_date' => date('Y-m-d', strtotime('-' . rand(1, 180) . ' days')),
                ':medical_conditions' => 'None',
                ':availability' => true
            ]);
        }
    }
    
    // Generate some blood requests
    for ($i = 0; $i < 10; $i++) {
        $request_query = "INSERT INTO blood_requests (recipient_id, blood_group, units_needed, urgency_level, 
                         hospital_name, hospital_address, contact_person, contact_number, status, created_at) 
                         VALUES (:recipient_id, :blood_group, :units_needed, :urgency_level, :hospital_name, 
                         :hospital_address, :contact_person, :contact_number, :status, NOW())";
        
        $urgency_levels = ['normal', 'urgent', 'critical'];
        $hospitals = ['Apollo Hospital', 'Fortis Hospital', 'Max Hospital', 'Medanta Hospital', 'AIIMS'];
        
        $request_stmt = $db->prepare($request_query);
        $request_stmt->execute([
            ':recipient_id' => rand(1, 30),
            ':blood_group' => $blood_groups[array_rand($blood_groups)],
            ':units_needed' => rand(1, 5),
            ':urgency_level' => $urgency_levels[array_rand($urgency_levels)],
            ':hospital_name' => $hospitals[array_rand($hospitals)],
            ':hospital_address' => $cities[array_rand($cities)] . ", India",
            ':contact_person' => $first_names[array_rand($first_names)] . ' ' . $last_names[array_rand($last_names)],
            ':contact_number' => '91' . rand(7000000000, 9999999999),
            ':status' => 'pending'
        ]);
    }
    
    // Update blood inventory with random units
    $inventory_query = "UPDATE blood_inventory SET units = :units WHERE blood_group = :blood_group";
    $inventory_stmt = $db->prepare($inventory_query);
    
    foreach ($blood_groups as $blood_group) {
        $inventory_stmt->execute([
            ':units' => rand(0, 20),
            ':blood_group' => $blood_group
        ]);
    }
    
    // Commit transaction
    $db->commit();
    
    echo "<div style='color: green; margin: 20px;'>";
    echo "Successfully generated sample data:<br>";
    echo "- 30 Users (mix of donors and recipients)<br>";
    echo "- 10 Blood requests<br>";
    echo "- Updated blood inventory<br>";
    echo "All users have password: user123</div>";
    
} catch (Exception $e) {
    // Rollback transaction on error
    $db->rollBack();
    echo "<div style='color: red; margin: 20px;'>";
    echo "Error generating sample data: " . $e->getMessage() . "<br>";
    echo "All changes have been rolled back.";
    echo "</div>";
}
?> 