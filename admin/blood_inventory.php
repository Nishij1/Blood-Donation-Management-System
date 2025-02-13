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

$error = '';
$success = '';

// Handle inventory update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $db->beginTransaction();
        
        foreach ($_POST['units'] as $blood_group => $units) {
            $query = "UPDATE blood_inventory SET units = :units WHERE blood_group = :blood_group";
            $stmt = $db->prepare($query);
            $stmt->execute([
                ':units' => $units,
                ':blood_group' => $blood_group
            ]);
        }
        
        $db->commit();
        $success = "Inventory updated successfully!";
    } catch (Exception $e) {
        $db->rollBack();
        $error = "Error updating inventory: " . $e->getMessage();
    }
}

// Get current inventory
$query = "SELECT * FROM blood_inventory ORDER BY blood_group";
$inventory = $db->query($query)->fetchAll();

include 'includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Blood Inventory Management</h4>

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Blood Group</th>
                                        <th>Available Units</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($inventory as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['blood_group']); ?></td>
                                        <td>
                                            <input type="number" class="form-control" 
                                                   name="units[<?php echo $item['blood_group']; ?>]" 
                                                   value="<?php echo htmlspecialchars($item['units']); ?>" 
                                                   min="0">
                                        </td>
                                        <td>
                                            <?php if($item['units'] < 5): ?>
                                                <span class="badge bg-danger">Low</span>
                                            <?php elseif($item['units'] < 10): ?>
                                                <span class="badge bg-warning">Moderate</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Sufficient</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#historyModal<?php echo $item['id']; ?>">
                                                View History
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Inventory</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 