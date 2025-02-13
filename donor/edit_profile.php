<?php
require_once '../config/config.php';
init_session();

if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'donor') {
    header("Location: ../login.php");
    exit();
}

require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

$error = '';
$success = '';

// Get current donor information
$query = "SELECT d.*, u.name, u.email 
          FROM donors d 
          JOIN users u ON d.user_id = u.id 
          WHERE u.id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$_SESSION['user_id']]);
$donor = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $db->beginTransaction();

        // Update user table
        $query = "UPDATE users SET 
                  name = :name,
                  email = :email
                  WHERE id = :user_id";
        $stmt = $db->prepare($query);
        $stmt->execute([
            ':name' => $_POST['name'],
            ':email' => $_POST['email'],
            ':user_id' => $_SESSION['user_id']
        ]);

        // Update donor table
        $query = "UPDATE donors SET 
                  blood_group = :blood_group,
                  age = :age,
                  weight = :weight,
                  contact_number = :contact_number,
                  address = :address,
                  medical_conditions = :medical_conditions
                  WHERE user_id = :user_id";
        $stmt = $db->prepare($query);
        $stmt->execute([
            ':blood_group' => $_POST['blood_group'],
            ':age' => $_POST['age'],
            ':weight' => $_POST['weight'],
            ':contact_number' => $_POST['contact_number'],
            ':address' => $_POST['address'],
            ':medical_conditions' => $_POST['medical_conditions'],
            ':user_id' => $_SESSION['user_id']
        ]);

        $db->commit();
        $success = "Profile updated successfully!";
        $_SESSION['user_name'] = $_POST['name'];
        
    } catch (Exception $e) {
        $db->rollBack();
        $error = "Error updating profile: " . $e->getMessage();
    }
}

include 'includes/header.php';
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Edit Profile</h4>

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" name="name" 
                                   value="<?php echo htmlspecialchars($donor['name']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" 
                                   value="<?php echo htmlspecialchars($donor['email']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Blood Group</label>
                            <select class="form-select" name="blood_group" required>
                                <option value="">Select Blood Group</option>
                                <?php
                                $blood_groups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
                                foreach ($blood_groups as $bg) {
                                    $selected = ($donor['blood_group'] == $bg) ? 'selected' : '';
                                    echo "<option value='$bg' $selected>$bg</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Age</label>
                            <input type="number" class="form-control" name="age" 
                                   value="<?php echo htmlspecialchars($donor['age']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Weight (kg)</label>
                            <input type="number" step="0.1" class="form-control" name="weight" 
                                   value="<?php echo htmlspecialchars($donor['weight']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Contact Number</label>
                            <input type="tel" class="form-control" name="contact_number" 
                                   value="<?php echo htmlspecialchars($donor['contact_number']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" name="address" rows="3"><?php echo htmlspecialchars($donor['address']); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Medical Conditions</label>
                            <textarea class="form-control" name="medical_conditions" rows="3"><?php echo htmlspecialchars($donor['medical_conditions']); ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Update Profile</button>
                        <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 