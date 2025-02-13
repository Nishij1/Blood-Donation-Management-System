<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/User.php';

init_session();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Debug information
        error_log("Registration attempt with data: " . print_r($_POST, true));

        // Validate inputs
        if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['role'])) {
            throw new Exception("All fields are required");
        }

        // Validate email format
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }

        // Validate password length
        if (strlen($_POST['password']) < 6) {
            throw new Exception("Password must be at least 6 characters long");
        }

        // Validate password match
        if ($_POST['password'] !== $_POST['confirm_password']) {
            throw new Exception("Passwords do not match");
        }

        // Validate role
        $allowed_roles = ['donor', 'recipient'];
        if (!in_array($_POST['role'], $allowed_roles)) {
            throw new Exception("Invalid role selected");
        }

        $database = new Database();
        $db = $database->getConnection();
        
        if (!$db) {
            throw new Exception("Database connection failed");
        }

        $user = new User($db);
        
        $result = $user->register(
            htmlspecialchars(trim($_POST['name'])),
            htmlspecialchars(trim($_POST['email'])),
            $_POST['password'],
            $_POST['role']
        );

        if ($result) {
            $success = "Registration successful! Please <a href='login.php'>login</a> to continue.";
        } else {
            throw new Exception("Registration failed for unknown reason");
        }
        
    } catch (Exception $e) {
        error_log("Registration error: " . $e->getMessage());
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Blood Donation System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="text-center mb-4">Create Account</h2>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>

                        <form method="POST" action="" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" name="name" required 
                                       autocomplete="name">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" class="form-control" name="email" required 
                                       autocomplete="email">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" required 
                                       autocomplete="new-password" minlength="6">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" name="confirm_password" required 
                                       autocomplete="new-password" minlength="6">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Register as</label>
                                <select class="form-select" name="role" required>
                                    <option value="">Select Role</option>
                                    <option value="donor">Blood Donor</option>
                                    <option value="recipient">Blood Recipient</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Register</button>
                        </form>
                        
                        <div class="text-center mt-3">
                            <p>Already have an account? <a href="login.php" class="text-decoration-none">Login here</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 