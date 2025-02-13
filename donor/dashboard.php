<?php
require_once '../config/config.php';
init_session();

// Check if user is logged in and is a donor
if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'donor') {
    header("Location: ../login.php");
    exit();
}

require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Get donor information
$query = "SELECT d.*, u.name, u.email 
          FROM donors d 
          JOIN users u ON d.user_id = u.id 
          WHERE u.id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$_SESSION['user_id']]);
$donor = $stmt->fetch();

// Get donation history
$query = "SELECT * FROM donations WHERE donor_id = ? ORDER BY created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute([$donor['id']]);
$donations = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <!-- Profile Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">My Profile</h5>
                    <div class="donor-info">
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
                        <p><strong>Blood Group:</strong> <?php echo $donor['blood_group'] ?? 'Not Set'; ?></p>
                        <p><strong>Last Donation:</strong> <?php echo $donor['last_donation_date'] ?? 'No donations yet'; ?></p>
                        <a href="edit_profile.php" class="btn btn-primary">Update Profile</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">My Donations</h5>
                    <div class="donation-stats">
                        <h2 class="text-primary"><?php echo count($donations); ?></h2>
                        <p>Total Donations</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-xl-4 col-md-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Quick Actions</h5>
                    <div class="d-grid gap-2">
                        <a href="schedule_donation.php" class="btn btn-success">Schedule New Donation</a>
                        <a href="view_requests.php" class="btn btn-info">View Blood Requests</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Donation History -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Donation History</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Blood Group</th>
                                    <th>Units</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($donations as $donation): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($donation['donation_date']); ?></td>
                                    <td><?php echo htmlspecialchars($donation['blood_group']); ?></td>
                                    <td><?php echo htmlspecialchars($donation['units']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $donation['status'] === 'completed' ? 'success' : 'warning'; ?>">
                                            <?php echo ucfirst(htmlspecialchars($donation['status'])); ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 