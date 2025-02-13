<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/User.php';

// Start session after config is loaded
init_session();

// Redirect if already logged in
if(isset($_SESSION['user_id'])) {
    if($_SESSION['user_role'] === 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: " . $_SESSION['user_role'] . "/dashboard.php");
    }
    exit();
}

$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        if(empty($_POST['email']) || empty($_POST['password'])) {
            throw new Exception("Please enter both email and password");
        }

        $database = new Database();
        $db = $database->getConnection();
        
        $user = new User($db);
        
        if($user->login($_POST['email'], $_POST['password'])) {
            // Redirect based on role
            if($_SESSION['user_role'] === 'admin') {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: " . $_SESSION['user_role'] . "/dashboard.php");
            }
            exit();
        } else {
            $error = "Invalid email or password";
        }
    } catch(Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Blood Donation System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-body p-4">
                        <h2 class="text-center mb-4">Login</h2>
                        
                        <?php if($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label">Email address</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>
                        
                        <div class="text-center mt-3">
                            <p>Don't have an account? <a href="register.php">Register here</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 