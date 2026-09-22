<?php
session_start();
require_once "../config/db.php";

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Get logged-in faculty ID
$facultyId = $_SESSION['faculty_staff_id'];

// Get faculty information
$sql = "SELECT f.id, f.full_name, f.department, f.role_title,
               u.email
        FROM faculty_staff f
        INNER JOIN users u ON u.faculty_staff_id = f.id
        WHERE f.id = :faculty_id";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    "faculty_id" => $facultyId
]);

$faculty = $stmt->fetch();

if (!$faculty) {
    echo "Faculty profile not found.";
    exit;
}


// Update profile
if (isset($_POST['update_profile'])) {

    $fullName = trim($_POST['full_name']);
    $department = trim($_POST['department']);
    $roleTitle = trim($_POST['role_title']);

    if ($fullName === "" || $department === "" || $roleTitle === "") {

        $error = "Please fill in all fields.";

    } else {

        try {

            $sql = "UPDATE faculty_staff
                    SET full_name = :full_name,
                        department = :department,
                        role_title = :role_title
                    WHERE id = :faculty_id";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                "full_name" => $fullName,
                "department" => $department,
                "role_title" => $roleTitle,
                "faculty_id" => $facultyId
            ]);

            header("Location: edit_profile.php?success=1");
            exit;

        } catch (PDOException $e) {

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

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;

            font-family: Arial, sans-serif;

            background:
                radial-gradient(
                    circle at top left,
                    rgba(139, 92, 246, 0.18),
                    transparent 35%
                ),
                radial-gradient(
                    circle at bottom right,
                    rgba(168, 85, 247, 0.15),
                    transparent 35%
                ),
                #080712;

            color: #e5e7eb;

            display: flex;
            justify-content: center;
            align-items: center;

            padding: 30px 15px;
        }


        /* Background glow */

        .glow {
            position: fixed;

            width: 250px;
            height: 250px;

            border-radius: 50%;

            background: rgba(139, 92, 246, 0.12);

            filter: blur(80px);

            pointer-events: none;
        }

        .glow-one {
            top: -80px;
            left: -80px;
        }

        .glow-two {
            bottom: -100px;
            right: -80px;
        }


        /* Main Card */

        .profile-card {
            width: 100%;
            max-width: 600px;

            background: rgba(18, 16, 32, 0.96);

            border: 1px solid rgba(139, 92, 246, 0.35);

            border-radius: 16px;

            padding: 30px;

            box-shadow:
                0 0 25px rgba(139, 92, 246, 0.12),
                0 20px 50px rgba(0, 0, 0, 0.45);
        }


        /* Header */

        .profile-header {
            text-align: center;

            margin-bottom: 25px;
        }

        .profile-icon {
            width: 70px;
            height: 70px;

            margin: 0 auto 15px;

            border-radius: 50%;

            display: flex;
            align-items: center;
            justify-content: center;

            background: rgba(139, 92, 246, 0.12);

            border: 1px solid rgba(139, 92, 246, 0.5);

            color: #a78bfa;

            font-size: 28px;

            box-shadow:
                0 0 18px rgba(139, 92, 246, 0.18);
        }

        .profile-header h1 {
            margin: 0;

            color: #f5f3ff;

            font-size: 25px;
        }

        .profile-header p {
            margin-top: 7px;

            color: #8f91a5;

            font-size: 13px;
        }


        /* Messages */

        .message {
            padding: 11px 13px;

            border-radius: 8px;

            margin-bottom: 18px;

            font-size: 13px;
        }

        .success {
            background: rgba(34, 197, 94, 0.08);

            border: 1px solid rgba(34, 197, 94, 0.3);

            color: #86efac;
        }

        .error {
            background: rgba(239, 68, 68, 0.08);

            border: 1px solid rgba(239, 68, 68, 0.3);

            color: #fca5a5;
        }


        /* Form */

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;

            margin-bottom: 7px;

            color: #c4b5fd;

            font-size: 13px;

            font-weight: bold;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;

            left: 14px;
            top: 50%;

            transform: translateY(-50%);

            color: #8b5cf6;

            font-size: 14px;
        }

        .form-control {
            width: 100%;

            padding: 12px 14px 12px 40px;

            background: rgba(10, 9, 20, 0.9);

            border: 1px solid rgba(139, 92, 246, 0.25);

            border-radius: 8px;

            color: #e5e7eb;

            font-size: 13px;

            outline: none;

            transition: 0.2s;
        }

        .form-control:focus {
            border-color: #8b5cf6;

            box-shadow:
                0 0 10px rgba(139, 92, 246, 0.18);
        }


        /* Email */

        .email-box {
            opacity: 0.55;

            cursor: not-allowed;
        }

        .email-info {
            margin-top: 5px;

            color: #77798c;

            font-size: 11px;
        }


        /* Buttons */

        .button-container {
            display: flex;

            gap: 10px;

            margin-top: 25px;
        }

        .btn {
            flex: 1;

            padding: 12px;

            border-radius: 8px;

            font-size: 13px;

            font-weight: bold;

            text-decoration: none;

            text-align: center;

            cursor: pointer;

            transition: 0.2s;
        }


        /* Save */

        .btn-save {
            border: none;

            background: linear-gradient(
                135deg,
                #7c3aed,
                #8b5cf6
            );

            color: white;

            box-shadow:
                0 0 12px rgba(139, 92, 246, 0.2);
        }

        .btn-save:hover {
            transform: translateY(-1px);

            box-shadow:
                0 0 18px rgba(139, 92, 246, 0.4);
        }


        /* Cancel */

        .btn-cancel {
            background: rgba(255, 255, 255, 0.05);

            border: 1px solid rgba(255, 255, 255, 0.12);

            color: #b9bbc9;
        }

        .btn-cancel:hover {
            background: rgba(255, 255, 255, 0.08);

            color: white;
        }


        /* Mobile */

        @media (max-width: 600px) {

            .profile-card {
                padding: 22px;
            }

            .button-container {
                flex-direction: column;
            }

            .profile-header h1 {
                font-size: 22px;
            }

        }

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


            <!-- Full Name -->

            <div class="form-group">

                <label for="full_name">
                    Full Name
                </label>

                <div class="input-wrapper">

                    <i class="fa-solid fa-user"></i>

                    <input
                        type="text"
                        name="full_name"
                        id="full_name"
                        class="form-control"
                        value="<?= htmlspecialchars($faculty['full_name']) ?>"
                        required
                    >

                </div>

            </div>


            <!-- Email -->

            <div class="form-group">

                <label for="email">
                    Email
                </label>

                <div class="input-wrapper">

                    <i class="fa-solid fa-envelope"></i>

                    <input
                        type="email"
                        id="email"
                        class="form-control email-box"
                        value="<?= htmlspecialchars($faculty['email']) ?>"
                        readonly
                    >

                </div>

                <div class="email-info">
                    Your email is used for login and cannot be changed here.
                </div>

            </div>


            <!-- Department -->

            <div class="form-group">

                <label for="department">
                    Department
                </label>

                <div class="input-wrapper">

                    <i class="fa-solid fa-building"></i>

                    <input
                        type="text"
                        name="department"
                        id="department"
                        class="form-control"
                        value="<?= htmlspecialchars($faculty['department']) ?>"
                        required
                    >

                </div>

            </div>


            <!-- Position -->

            <div class="form-group">

                <label for="role_title">
                    Position
                </label>

                <div class="input-wrapper">

                    <i class="fa-solid fa-briefcase"></i>

                    <input
                        type="text"
                        name="role_title"
                        id="role_title"
                        class="form-control"
                        value="<?= htmlspecialchars($faculty['role_title']) ?>"
                        required
                    >

                </div>

            </div>


            <!-- Buttons -->

            <div class="button-container">

                <a href="dashboard.php" class="btn btn-cancel">

                    <i class="fa-solid fa-arrow-left"></i>

                    Cancel

                </a>


                <button
                    type="submit"
                    name="update_profile"
                    class="btn btn-save"
                >

                    <i class="fa-solid fa-floppy-disk"></i>

                    Save Changes

                </button>

            </div>


        </form>

    </div>

</body>

</html>