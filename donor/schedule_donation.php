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

// Get donor information
$query = "SELECT * FROM donors WHERE user_id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$_SESSION['user_id']]);
$donor = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        if (empty($donor['blood_group'])) {
            throw new Exception("Please update your blood group in your profile first");
        }

        $query = "INSERT INTO donations (donor_id, blood_group, units, donation_date, status) 
                  VALUES (:donor_id, :blood_group, :units, :donation_date, 'scheduled')";
        $stmt = $db->prepare($query);
        $stmt->execute([
            ':donor_id' => $donor['id'],
            ':blood_group' => $donor['blood_group'],
            ':units' => $_POST['units'],
            ':donation_date' => $_POST['donation_date']
        ]);

        $success = "Donation scheduled successfully!";
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

include 'includes/header.php';
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Schedule Blood Donation</h4>

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Donation Date</label>
                            <input type="date" class="form-control" name="donation_date" 
                                   min="<?php echo date('Y-m-d'); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Units to Donate</label>
                            <select class="form-select" name="units" required>
                                <option value="1">1 Unit</option>
                                <option value="2">2 Units</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Schedule Donation</button>
                        <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 