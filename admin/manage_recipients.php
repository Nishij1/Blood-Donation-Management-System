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

// Get all recipients
$query = "SELECT * FROM users WHERE role = 'recipient' ORDER BY created_at DESC";
$recipients = $db->query($query)->fetchAll();

include 'includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Manage Recipients</h4>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Joined Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($recipients as $recipient): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($recipient['name']); ?></td>
                                <td><?php echo htmlspecialchars($recipient['email']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $recipient['status'] === 'active' ? 'success' : 'danger'; ?>">
                                        <?php echo ucfirst($recipient['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('Y-m-d', strtotime($recipient['created_at'])); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="viewRecipient(<?php echo $recipient['id']; ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-warning" onclick="toggleStatus(<?php echo $recipient['id']; ?>)">
                                        <i class="fas fa-power-off"></i>
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
function viewRecipient(id) {
    window.location.href = 'view_recipient.php?id=' + id;
}

function toggleStatus(id) {
    if(confirm('Are you sure you want to change this recipient\'s status?')) {
        fetch('ajax/toggle_recipient_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({id: id})
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