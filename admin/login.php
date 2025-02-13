<?php
require_once '../config/config.php';
init_session();

// Redirect if already logged in as admin
if(isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'admin') {
    header("Location: dashboard.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    try {
        $query = "SELECT * FROM users WHERE email = :email AND role = 'admin' LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if ($user['status'] === 'inactive') {
                $error = "This account has been deactivated";
            } else {
                // For debugging, let's see what's being compared
                error_log('Input Password: ' . $password);
                error_log('Stored Hash: ' . $user['password']);
                
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_role'] = $user['role'];
                    
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $error = "Invalid password";
                }
            }
        } else {
            $error = "Admin account not found";
        }
    } catch (Exception $e) {
        error_log("Admin Login Error: " . $e->getMessage());
        $error = "An error occurred. Please try again later.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Blood Donation System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .card-header {
            background-color: #dc3545;
            color: white;
            text-align: center;
            padding: 20px;
            border-radius: 10px 10px 0 0;
        }
        .btn-login {
            background-color: #dc3545;
            border-color: #dc3545;
            width: 100%;
        }
        .btn-login:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }
        .alert {
            border-radius: 8px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-user-shield"></i> Admin Login</h4>
                </div>
                <div class="card-body p-4">
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                                       required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-login text-white">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </button>
                    </form>
                </div>
            </div>
            <div class="text-center mt-3">
                <a href="../index.php" class="text-decoration-none">
                    <i class="fas fa-arrow-left"></i> Back to Homepage
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 