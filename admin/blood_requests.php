<?php
require_once '../config/config.php';
init_session();

if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Get all blood requests
$query = "SELECT br.*, u.name as recipient_name 
          FROM blood_requests br 
          JOIN users u ON br.recipient_id = u.id 
          ORDER BY br.urgency_level DESC, br.created_at DESC";
$requests = $db->query($query)->fetchAll();

include 'includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Blood Requests</h4>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Recipient</th>
                                <th>Blood Group</th>
                                <th>Units</th>
                                <th>Hospital</th>
                                <th>Urgency</th>
                                <th>Status</th>
                                <th>Actions</th>
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
                                        <?php echo ucfirst($request['urgency_level']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-<?php 
                                        echo $request['status'] === 'completed' ? 'success' : 
                                            ($request['status'] === 'pending' ? 'warning' : 'danger'); 
                                    ?>">
                                        <?php echo ucfirst($request['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="viewRequest(<?php echo $request['id']; ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-success" onclick="updateStatus(<?php echo $request['id']; ?>)">
                                        <i class="fas fa-check"></i>
                                    </button>
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

<script>
function viewRequest(id) {
    window.location.href = 'view_request.php?id=' + id;
}

function updateStatus(id) {
    // Implement status update logic
    const status = prompt('Enter new status (pending/approved/completed/cancelled):');
    if(status) {
        fetch('ajax/update_request_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({id: id, status: status})
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                location.reload();
            } else {
                alert('Error updating status');
            }
        });
    }
}
</script>

<?php include 'includes/footer.php'; ?> 