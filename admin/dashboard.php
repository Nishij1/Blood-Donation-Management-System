<?php
require_once '../config/config.php';
init_session();

// Check if user is logged in and is an admin
if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Get statistics
$stats = [
    'donors' => $db->query("SELECT COUNT(*) FROM donors")->fetchColumn(),
    'recipients' => $db->query("SELECT COUNT(*) FROM users WHERE role = 'recipient'")->fetchColumn(),
    'pending_requests' => $db->query("SELECT COUNT(*) FROM blood_requests WHERE status = 'pending'")->fetchColumn(),
    'total_donations' => $db->query("SELECT COUNT(*) FROM donations WHERE status = 'completed'")->fetchColumn()
];

// Get blood inventory
$inventory_query = "SELECT blood_group, units FROM blood_inventory ORDER BY blood_group";
$inventory = $db->query($inventory_query)->fetchAll();

// Get recent donations
$recent_donations = $db->query(
    "SELECT d.*, u.name as donor_name, u.email 
     FROM donations d 
     JOIN donors dr ON d.donor_id = dr.id 
     JOIN users u ON dr.user_id = u.id 
     ORDER BY d.created_at DESC LIMIT 5"
)->fetchAll();

include 'includes/header.php';
?>

<div class="container-fluid py-4">
    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h6 class="text-muted mb-1">Total Donors</h6>
                            <span class="h3 font-weight-bold mb-0"><?php echo $stats['donors']; ?></span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-danger text-white rounded-circle shadow">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h6 class="text-muted mb-1">Total Recipients</h6>
                            <span class="h3 font-weight-bold mb-0"><?php echo $stats['recipients']; ?></span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-info text-white rounded-circle shadow">
                                <i class="fas fa-user-friends"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h6 class="text-muted mb-1">Pending Requests</h6>
                            <span class="h3 font-weight-bold mb-0"><?php echo $stats['pending_requests']; ?></span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-warning text-white rounded-circle shadow">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h6 class="text-muted mb-1">Total Donations</h6>
                            <span class="h3 font-weight-bold mb-0"><?php echo $stats['total_donations']; ?></span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-success text-white rounded-circle shadow">
                                <i class="fas fa-tint"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Blood Inventory and Recent Activities -->
    <div class="row">
        <!-- Blood Inventory -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Blood Inventory</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Blood Group</th>
                                    <th>Available Units</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($inventory as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['blood_group']); ?></td>
                                    <td><?php echo htmlspecialchars($item['units']); ?></td>
                                    <td>
                                        <?php if($item['units'] < 5): ?>
                                            <span class="badge bg-danger">Low</span>
                                        <?php elseif($item['units'] < 10): ?>
                                            <span class="badge bg-warning">Moderate</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Sufficient</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Donations -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Donations</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Donor</th>
                                    <th>Blood Group</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($recent_donations as $donation): ?>
                                <tr>
                                    <td><?php echo date('Y-m-d', strtotime($donation['created_at'])); ?></td>
                                    <td><?php echo htmlspecialchars($donation['donor_name']); ?></td>
                                    <td><?php echo htmlspecialchars($donation['blood_group']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo $donation['status'] === 'completed' ? 'success' : 
                                                ($donation['status'] === 'scheduled' ? 'warning' : 'danger'); 
                                        ?>">
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