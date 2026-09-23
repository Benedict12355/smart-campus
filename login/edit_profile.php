<?php
session_start();
require_once "../config/db.php";

// Authentication Check
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

$facultyId = $_SESSION['faculty_staff_id'];

// CSRF Token Generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Fetch Faculty Information
$sql = "SELECT f.id, f.full_name, f.department, f.role_title, u.email
        FROM faculty_staff f
        INNER JOIN users u ON u.faculty_staff_id = f.id
        WHERE f.id = :faculty_id";

$stmt = $pdo->prepare($sql);
$stmt->execute([":faculty_id" => $facultyId]);
$faculty = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$faculty) {
    echo "Faculty profile not found.";
    exit;
}

// Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    // CSRF Check
    $userToken = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $userToken)) {
        die('Invalid CSRF token.');
    }

    $fullName   = trim(filter_input(INPUT_POST, 'full_name', FILTER_UNSAFE_RAW) ?? '');
    $department = trim(filter_input(INPUT_POST, 'department', FILTER_UNSAFE_RAW) ?? '');
    $roleTitle  = trim(filter_input(INPUT_POST, 'role_title', FILTER_UNSAFE_RAW) ?? '');

    if ($fullName === "" || $department === "" || $roleTitle === "") {
        $error = "Please fill in all fields.";
    } else {
        try {
            $pdo->beginTransaction();

            $updateSql = "UPDATE faculty_staff
                          SET full_name = :full_name,
                              department = :department,
                              role_title = :role_title
                          WHERE id = :faculty_id";

            $updateStmt = $pdo->prepare($updateSql);
            $updateStmt->execute([
                ":full_name"  => $fullName,
                ":department" => $department,
                ":role_title" => $roleTitle,
                ":faculty_id" => $facultyId
            ]);

            $pdo->commit();
            header("Location: edit_profile.php?success=1");
            exit;
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = "Something went wrong while updating your profile.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/style/edit_profile.css">
    <style>
       
    </style>
</head>
<body>

    <div class="glow glow-one"></div>
    <div class="glow glow-two"></div>

    <div class="profile-card">
        <!-- Header -->
        <div class="profile-header">
            <div class="profile-icon">
                <i class="fa-solid fa-user-pen"></i>
            </div>
            <h1>Edit Profile</h1>
            <p>Update your faculty information</p>
        </div>

        <!-- Success Message -->
        <?php if (isset($_GET['success'])): ?>
            <div class="message success">
                <i class="fa-solid fa-circle-check"></i>
                Profile updated successfully!
            </div>
        <?php endif; ?>

        <!-- Error Message -->
        <?php if (isset($error)): ?>
            <div class="message error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Form -->
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <!-- Full Name -->
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-user"></i>
                    <input type="text" name="full_name" id="full_name" class="form-control"
                        value="<?= htmlspecialchars($faculty['full_name'] ?? '') ?>" required>
                </div>
            </div>

            <!-- Email -->
            <div class="form-group">
                <label for="email">Email</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-envelope"></i>
                    <input type="email" id="email" class="form-control email-box"
                        value="<?= htmlspecialchars($faculty['email'] ?? '') ?>" readonly>
                </div>
                <div class="email-info">
                    Your email is used for login and cannot be changed here.
                </div>
            </div>

            <!-- Department -->
            <div class="form-group">
                <label for="department">Department</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-building"></i>
                    <input type="text" name="department" id="department" class="form-control" 
                    value="<?= htmlspecialchars($faculty['department'] ?? '') ?>" required>
                </div>
            </div>

            <!-- Position -->
            <div class="form-group">
                <label for="role_title">Position</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-briefcase"></i>
                    <input type="text" name="role_title" id="role_title" class="form-control" 
                    value="<?= htmlspecialchars($faculty['role_title'] ?? '') ?>" required>
                </div>
            </div>

            <!-- Buttons -->
            <div class="button-container">
                <a href="dashboard.php" class="btn btn-cancel">
                    <i class="fa-solid fa-arrow-left"></i>
                    Return
                </a>
                <button type="submit" name="update_profile" class="btn btn-save">
                    <i class="fa-solid fa-floppy-disk"></i>Save Changes
                </button>
            </div>
        </form>
    </div>
</body>
</html>