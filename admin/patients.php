<?php 
include 'includes/header.php';
require_once '../includes/Patient.php';

$database = new Database();
$db = $database->getConnection();
$patient = new Patient($db);
$patients = $patient->getAllPatients();
?>

<div class="container-fluid">
    <h2 class="mb-4">Patient Management</h2>
    
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">All Patients</h5>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPatientModal">
                Add New Patient
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Date of Birth</th>
                            <th>Blood Group</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($patients as $patient): ?>
                        <tr>
                            <td><?php echo $patient['id']; ?></td>
                            <td><?php echo htmlspecialchars($patient['name']); ?></td>
                            <td><?php echo htmlspecialchars($patient['email']); ?></td>
                            <td><?php echo $patient['date_of_birth']; ?></td>
                            <td><?php echo $patient['blood_group']; ?></td>
                            <td>
                                <button class="btn btn-sm btn-info" onclick="viewPatient(<?php echo $patient['id']; ?>)">View</button>
                                <button class="btn btn-sm btn-warning" onclick="editPatient(<?php echo $patient['id']; ?>)">Edit</button>
                                <button class="btn btn-sm btn-danger" onclick="deletePatient(<?php echo $patient['id']; ?>)">Delete</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 