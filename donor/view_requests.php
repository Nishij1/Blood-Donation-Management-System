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

// Get donor's blood group
$query = "SELECT blood_group FROM donors WHERE user_id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$_SESSION['user_id']]);
$donor = $stmt->fetch();

// Get matching blood requests
$query = "SELECT br.*, u.name as recipient_name 
          FROM blood_requests br 
          JOIN users u ON br.recipient_id = u.id 
          WHERE br.status = 'pending' 
          AND br.blood_group = :blood_group 
          ORDER BY br.urgency_level DESC, br.created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute([':blood_group' => $donor['blood_group']]);
$requests = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Blood Requests</h4>
                    
                    <?php if (empty($donor['blood_group'])): ?>
                        <div class="alert alert-warning">
                            Please update your blood group in your profile to see matching requests.
                        </div>
                    <?php elseif (empty($requests)): ?>
                        <div class="alert alert-info">
                            No matching blood requests found for your blood group (<?php echo $donor['blood_group']; ?>).
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Recipient</th>
                                        <th>Blood Group</th>
                                        <th>Units Needed</th>
                                        <th>Hospital</th>
                                        <th>Urgency</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($requests as $request): ?>
                                    <tr>
                                        <td><?php echo date('Y-m-d', strtotime($request['created_at'])); ?></td>
                                        <td><?php echo htmlspecialchars($request['recipient_name']); ?></td>
                                        <td><?php echo htmlspecialchars($request['blood_group']); ?></td>
                                        <td><?php echo htmlspecialchars($request['units_needed']); ?></td>
                                        <td><?php echo htmlspecialchars($request['hospital_name']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $request['urgency_level'] === 'critical' ? 'danger' : 
                                                    ($request['urgency_level'] === 'urgent' ? 'warning' : 'info'); 
                                            ?>">
                                                <?php echo ucfirst(htmlspecialchars($request['urgency_level'])); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="respond_request.php?id=<?php echo $request['id']; ?>" 
                                               class="btn btn-sm btn-primary">Respond</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 