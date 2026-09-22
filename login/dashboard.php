<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php');
    exit;
}

$facultyId = $_SESSION['faculty_staff_id'];
$message = '';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $status = $_POST['status'];
    $location = trim($_POST['location']);

    $stmt = $pdo->prepare("
        INSERT INTO faculty_status (faculty_id, status, location)
        VALUES (:fid, :status, :location)
        ON DUPLICATE KEY UPDATE status = :status2, location = :location2
    ");
    $stmt->execute([
        ':fid' => $facultyId, ':status' => $status, ':location' => $location,
        ':status2' => $status, ':location2' => $location,
    ]);
    header("Location: dashboard.php");
    exit;
}

// Handle auto-update toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_auto'])) {
    $auto = isset($_POST['auto_update']) ? 1 : 0;
    $stmt = $pdo->prepare("UPDATE faculty_status SET auto_update = :auto WHERE faculty_id = :fid");
    $stmt->execute([':auto' => $auto, ':fid' => $facultyId]);
    header("Location: dashboard.php");
    exit;
}

// Handle note update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_note'])) {
    $note = trim($_POST['note']);
    $stmt = $pdo->prepare("UPDATE faculty_status SET note = :note WHERE faculty_id = :fid");
    $stmt->execute([':note' => $note, ':fid' => $facultyId]);
    header("Location: dashboard.php");
    exit;
}

// Handle class deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_class'])) {
    $stmt = $pdo->prepare("DELETE FROM class_schedule WHERE id = :id AND faculty_id = :fid");
    $stmt->execute([':id' => $_POST['delete_class_id'], ':fid' => $facultyId]);
    header("Location: dashboard.php");
    exit;
}

// Fetch profile info
$stmt = $pdo->prepare("SELECT * FROM faculty_staff WHERE id = :id");
$stmt->execute([':id' => $facultyId]);
$faculty = $stmt->fetch();

// Fetch email
$stmt = $pdo->prepare("SELECT email FROM users WHERE faculty_staff_id = :id");
$stmt->execute([':id' => $facultyId]);
$account = $stmt->fetch();

// Fetch current status/location/note/auto_update
$stmt = $pdo->prepare("SELECT * FROM faculty_status WHERE faculty_id = :id");
$stmt->execute([':id' => $facultyId]);
$currentStatus = $stmt->fetch();

// Fetch class schedule
$stmt = $pdo->prepare("SELECT * FROM class_schedule WHERE faculty_id = :id ORDER BY day_of_week, start_time");
$stmt->execute([':id' => $facultyId]);
$classes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Account</title>
    <link rel="stylesheet" href="dashboardstyle.css">
</head>
<body>

<div class="container">

    <!-- ================= HEADER ================= -->
    <div class="header">
        <h1>Faculty Account</h1>
        <a href="process.php?logout=true" class="logout">Logout</a>
    </div>

    <!-- ================= PROFILE ================= -->
    <div class="profile">
        <div class="profile-top">
            <div class="profile-picture">👤</div>

            <div class="profile-info">
                <h2><?= htmlspecialchars($faculty['full_name']) ?></h2>
                <p><?= htmlspecialchars($faculty['role_title']) ?> · <?= htmlspecialchars($faculty['department']) ?></p>
                <p>Email: <?= htmlspecialchars($account['email']) ?></p>
            </div>

            <div class="profile-actions">
                <div class="last-updated">
                    Last Updated:
                    <strong>
                        <?= $currentStatus && $currentStatus['updated_at']
                              ? date('F j, Y g:i A', strtotime($currentStatus['updated_at']))
                              : 'Never' ?>
                    </strong>
                </div>
                <a href="edit_profile.php" class="edit-profile-btn">
    <i class="fa-solid fa-user-pen"></i>
    Edit Profile
</a>
            </div>
        </div>
    </div>

    <!-- ================= MAIN GRID ================= -->
    <div class="grid">

        <!-- ================= LOCATION & STATUS ================= -->
        <div class="card">
            <h3>Current Location & Status</h3>

            <form method="POST">
                <div class="setting">
                    <label>Current Location</label>
                    <select name="location">
                        <?php
                        $locations = ['Faculty Room', 'Computer Laboratory 1', 'Computer Laboratory 2', 'Library', 'Office', 'Classroom', 'Outside Campus'];
                        foreach ($locations as $loc): ?>
                            <option <?= ($currentStatus['location'] ?? '') === $loc ? 'selected' : '' ?>><?= $loc ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="setting">
                    <label>Current Status</label>
                    <select name="status">
                        <?php
                        $statuses = [
                            'available' => 'Available', 'in_class' => 'In Class', 'busy' => 'Busy',
                            'in_meeting' => 'In a Meeting', 'consultation_hours' => 'Consultation Hours',
                            'out_of_campus' => 'Out of Campus',
                        ];
                        foreach ($statuses as $val => $label): ?>
                            <option value="<?= $val ?>" <?= ($currentStatus['status'] ?? '') === $val ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" name="update_status" value="1" class="save-btn">Save Status</button>
                <button type="button" class="update-btn" id="updateNowBtn">Update Now</button>
            </form>

            <form method="POST" id="autoUpdateForm">
                <input type="hidden" name="toggle_auto" value="1">
                <div class="auto-update">
                    <div class="auto-update-text">
                        <h4>Auto Update</h4>
                        <p>Automatically update your class, location and availability.</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" name="auto_update" id="autoUpdate"
                               <?= ($currentStatus['auto_update'] ?? 0) == 1 ? 'checked' : '' ?>
                               onchange="document.getElementById('autoUpdateForm').submit()">
                        <span class="slider"></span>
                    </label>
                </div>
            </form>
        </div>
        <!-- ^^ card closed here — this was the missing tag ^^ -->

        <!-- ================= PERSONAL NOTE ================= -->
        <div class="card">
            <h3>Personal Note</h3>
            <form method="POST">
                <textarea name="note" placeholder="Write a personal note..."><?= htmlspecialchars($currentStatus['note'] ?? '') ?></textarea>
                <div class="note-buttons">
                    <button type="submit" name="update_note" value="1" class="save-btn">Save Note</button>
                </div>
            </form>
        </div>

        <!-- ================= CLASS SCHEDULE ================= -->
        <div class="card schedule-container">

            <div class="schedule-header">
                <div>
                    <h3>Class Schedule</h3>
                    <p class="schedule-description">Weekly class schedule</p>
                </div>
                <div class="semester-control">
                    <button type="button" class="add-class-btn" id="addClassBtn">+ Add Class</button>
                </div>
            </div>

            <div class="calendar-wrapper">
                <div class="calendar">

                    <!-- TIME COLUMN -->
                    <div class="time-column">
                        <div class="calendar-header-space"></div>
                        <?php for ($h = 7; $h <= 21; $h++):
                            $displayHour = $h % 12 === 0 ? 12 : $h % 12;
                            $ampm = $h < 12 ? 'AM' : 'PM';
                        ?>
                            <div class="time"><?= $displayHour ?>:00 <?= $ampm ?></div>
                        <?php endfor; ?>
                    </div>

                    <!-- DAY COLUMNS -->
                    <?php
                    $days = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday'];
                    foreach ($days as $dayNum => $dayName):
                    ?>
                        <div class="day-column">
                            <div class="day-header"><?= $dayName ?></div>
                            <div class="calendar-body">
                                <?php foreach ($classes as $class): ?>
                                    <?php if ($class['day_of_week'] == $dayNum):
                                        $startHour = (int)date('H', strtotime($class['start_time']));
                                        $startMin = (int)date('i', strtotime($class['start_time']));
                                        $top = (($startHour - 7) * 60) + $startMin;
                                        $durationMinutes = (strtotime($class['end_time']) - strtotime($class['start_time'])) / 60;
                                    ?>
                                        <div class="class-block" style="top: <?= $top ?>px; height: <?= $durationMinutes ?>px;">
                                            <strong><?= htmlspecialchars($class['subject']) ?></strong>
                                            <span><?= date('g:i A', strtotime($class['start_time'])) ?> - <?= date('g:i A', strtotime($class['end_time'])) ?></span>
                                            <span><?= htmlspecialchars($class['room']) ?></span>
                                            <span><?= htmlspecialchars($class['section']) ?></span>
                                            <div class="class-actions">
                                                <button type="button" class="edit-class" data-id="<?= $class['id'] ?>">Edit</button>
                                                <form method="POST" style="display:inline" onsubmit="return confirm('Delete this class schedule?')">
                                                    <input type="hidden" name="delete_class_id" value="<?= $class['id'] ?>">
                                                    <button type="submit" name="delete_class" value="1" class="delete-class">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>

                </div>
            </div>
        </div>

    </div>
</div>

</body>
</html>