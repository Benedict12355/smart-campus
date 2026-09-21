<?php
session_start();
// require '../config/db.php';
require 'config/db.php';
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php');
    exit;
}

$facultyId = $_SESSION['faculty_staff_id'];
$message = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $fullName = trim($_POST['full_name']);
    $department = trim($_POST['department']);
    $roleTitle = trim($_POST['role_title']);

    $stmt = $pdo->prepare("UPDATE faculty_staff SET full_name = :name, department = :dept, role_title = :role WHERE id = :id");
    $stmt->execute([
        ':name' => $fullName,
        ':dept' => $department,
        ':role' => $roleTitle,
        ':id' => $facultyId,
    ]);
    $message = 'Profile updated.';
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $status = $_POST['status'] ?? '';
    $location = trim($_POST['location'] ?? '');
    $note = trim($_POST['note'] ?? '');

    $allowed = ['available', 'in_class', 'busy', 'in_meeting', 'consultation_hours', 'out_of_campus'];
    if (in_array($status, $allowed, true) && $facultyId) {
        $stmt = $pdo->prepare("
            INSERT INTO faculty_status (faculty_id, status, location, note)
            VALUES (:faculty_id, :status, :location, :note)
            ON DUPLICATE KEY UPDATE status = :status2, location = :location2, note = :note2
        ");
        $stmt->execute([
            ':faculty_id' => $facultyId,
            ':status'     => $status,
            ':location'   => $location,
            ':note'       => $note,
            ':status2'    => $status,
            ':location2'  => $location,
            ':note2'      => $note,
        ]);
        $message = 'Your status has been updated.';
    }
}

// Get current status
$currentStatus = null;
if ($facultyId) {
    $stmt = $pdo->prepare("SELECT * FROM faculty_status WHERE faculty_id = :id");
    $stmt->execute([':id' => $facultyId]);
    $currentStatus = $stmt->fetch();
}

// Get profile info
$stmt = $pdo->prepare("SELECT * FROM faculty_staff WHERE id = :id");
$stmt->execute([':id' => $facultyId]);
$faculty = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Dashboard</title>
<link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
<style>
    body { background: #0a0e27; color: #e8eaed; font-family: Arial, sans-serif; }
    .card { background: #10152a; border: 1px solid #232a45; border-radius: 14px; color: #e8eaed; }
    .form-control, .form-select { background: #131829; border: 1px solid #2a3150; color: #e8eaed; }
    .form-control:focus, .form-select:focus { background: #131829; color: #fff; border-color: #7c2ae8; }
    label { color: #8b92ab; }
    .btn-primary { background: linear-gradient(90deg, #7c2ae8, #a742f0); border: none; }
    .text-muted-light { color: #8b92ab; }
</style>
</head>
<body>
<div class="container py-5" style="max-width: 600px;">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Welcome, <?= htmlspecialchars($faculty['full_name']) ?></h2>
        <a href="process.php?logout=true" class="btn btn-outline-light btn-sm"
           onclick="return confirm('Are you sure you want to logout?');">
            Log Out
        </a>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Status Update -->
    <div class="card p-4 mb-4">
        <h5 class="mb-2">Update Your Availability</h5>
        <?php if ($currentStatus && $currentStatus['updated_at']): ?>
            <p class="text-muted-light small mb-3">
                Last updated: <?= date('F j, Y \a\t g:i A', strtotime($currentStatus['updated_at'])) ?>
            </p>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select" required>
                    <?php
                    $options = [
                        'available' => 'Available',
                        'in_class' => 'In Class',
                        'busy' => 'Busy',
                        'in_meeting' => 'In Meeting',
                        'consultation_hours' => 'Consultation Hours',
                        'out_of_campus' => 'Out of Campus',
                    ];
                    $selected = $currentStatus['status'] ?? '';
                    foreach ($options as $value => $label):
                    ?>
                        <option value="<?= $value ?>" <?= $selected === $value ? 'selected' : '' ?>>
                            <?= $label ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Location</label>
                <input type="text" name="location" class="form-control"
                       value="<?= htmlspecialchars($currentStatus['location'] ?? '') ?>"
                       placeholder="e.g. Room 201, Faculty Office 3">
            </div>
            <div class="mb-3">
                <label class="form-label">Note (optional)</label>
                <input type="text" name="note" class="form-control" maxlength="150"
                       value="<?= htmlspecialchars($currentStatus['note'] ?? '') ?>"
                       placeholder="e.g. Back by 2pm, available for consultation only">
            </div>
            <button type="submit" name="update_status" value="1" class="btn btn-primary">
                Update Status
            </button>
        </form>
    </div>

    <!-- Profile Update -->
    <div class="card p-4 mb-4">
        <h5 class="mb-3">Edit Your Profile</h5>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="full_name" class="form-control"
                       value="<?= htmlspecialchars($faculty['full_name']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Department</label>
                <input type="text" name="department" class="form-control"
                       value="<?= htmlspecialchars($faculty['department']) ?>"
                       placeholder="e.g. IT Department">
            </div>
            <div class="mb-3">
                <label class="form-label">Role</label>
                <input type="text" name="role_title" class="form-control"
                       value="<?= htmlspecialchars($faculty['role_title']) ?>"
                       placeholder="e.g. Professor, Instructor, Staff">
            </div>
            <button type="submit" name="update_profile" value="1" class="btn btn-primary">
                Save Profile
            </button>
        </form>
    </div>

    <p><a href="../public/faculty.php" style="color:#a78bfa;">View the public kiosk</a></p>

</div>
</body>
</html>