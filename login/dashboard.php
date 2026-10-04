<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php');
    exit;
}

$facultyId = $_SESSION['faculty_staff_id'];

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userToken = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $userToken)) {
        die('Invalid CSRF token.');
    }

    if (isset($_POST['update_status'])) {
        $status = filter_input(INPUT_POST, 'status', FILTER_UNSAFE_RAW) ?? '';
        $location = trim(filter_input(INPUT_POST, 'location', FILTER_UNSAFE_RAW) ?? '');

        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("
                INSERT INTO faculty_status (faculty_id, status, location)
                VALUES (:fid, :status, :location)
                ON DUPLICATE KEY UPDATE
                    status = :status2, location = :location2, updated_at = CURRENT_TIMESTAMP
            ");
            $stmt->execute([
                ':fid' => $facultyId, ':status' => $status, ':location' => $location,
                ':status2' => $status, ':location2' => $location
            ]);
            $pdo->commit();
            $_SESSION['success_message'] = 'Status and location have been saved.';
        } catch (Exception $e) {
            $pdo->rollBack();
            $_SESSION['error_message'] = 'Failed to update status and location.';
        }
        header("Location: dashboard.php");
        exit;
    }

    if (isset($_POST['toggle_auto'])) {
        $auto = isset($_POST['auto_update']) ? 1 : 0;
        try {
            $stmt = $pdo->prepare("UPDATE faculty_status SET auto_update = :auto WHERE faculty_id = :fid");
            $stmt->execute([':auto' => $auto, ':fid' => $facultyId]);
            $_SESSION['success_message'] = $auto ? 'Auto Update has been enabled.' : 'Auto Update has been disabled.';
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Failed to update auto-update setting.';
        }
        header("Location: dashboard.php");
        exit;
    }

    if (isset($_POST['update_note'])) {
        $note = trim(filter_input(INPUT_POST, 'note', FILTER_UNSAFE_RAW) ?? '');
        try {
            $stmt = $pdo->prepare("UPDATE faculty_status SET note = :note WHERE faculty_id = :fid");
            $stmt->execute([':note' => $note, ':fid' => $facultyId]);
            $_SESSION['success_message'] = 'Personal note has been posted.';
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Failed to update note.';
        }
        header("Location: dashboard.php");
        exit;
    }
}

$stmt = $pdo->prepare("SELECT * FROM faculty_staff WHERE id = :id");
$stmt->execute([':id' => $facultyId]);
$faculty = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

$stmt = $pdo->prepare("SELECT email FROM users WHERE faculty_staff_id = :id");
$stmt->execute([':id' => $facultyId]);
$account = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

$stmt = $pdo->prepare("SELECT * FROM faculty_status WHERE faculty_id = :id");
$stmt->execute([':id' => $facultyId]);
$currentStatus = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

$stmt = $pdo->prepare("SELECT * FROM faculty_classes WHERE faculty_id = :id ORDER BY day_of_week, start_time");
$stmt->execute([':id' => $facultyId]);
$classes = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

$totalClasses = count($classes);

// Find next upcoming class (today or later in the week)
$nextClass = null;
$nowDow = (int)date('N');
$nowTime = date('H:i:s');
foreach ($classes as $c) {
    if ($c['day_of_week'] == $nowDow && $c['start_time'] > $nowTime) {
        $nextClass = $c;
        break;
    }
}
if (!$nextClass && $totalClasses > 0) {
    foreach ($classes as $c) {
        if ($c['day_of_week'] > $nowDow) { $nextClass = $c; break; }
    }
    if (!$nextClass) $nextClass = $classes[0];
}

$successMessage = $_SESSION['success_message'] ?? '';
$errorMessage   = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Dashboard</title>
    <link rel="stylesheet" href="../assets/style/shell.css">
    <link rel="stylesheet" href="dashboardstyle.css">
</head>
<body>

<div class="app-layout">

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">Faculty Portal</div>
        <a href="dashboard.php" class="active">📊 Dashboard</a>
        <a href="classes.php">📅 My Classes</a>
        <a href="edit_profile.php">👤 Edit Profile</a>
        <a href="process.php?logout=true" class="logout-link">🚪 Logout</a>
    </div>

    <!-- Main content -->
    <div class="main-area">

        <div class="topbar">
            <div>
                <h1>Welcome, <?= htmlspecialchars($faculty['full_name'] ?? 'N/A') ?></h1>
                <div class="topbar-sub">
                    <?= htmlspecialchars($faculty['role_title'] ?? 'Faculty') ?> · <?= htmlspecialchars($faculty['department'] ?? 'General') ?>
                </div>
            </div>
        </div>

        <!-- Stat cards -->
        <div class="stat-row">
            <div class="stat-card stat-purple">
                <div class="stat-icon">🟢</div>
                <div class="stat-value"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $currentStatus['status'] ?? 'Not Set'))) ?></div>
                <div class="stat-label">Current Status</div>
            </div>
            <div class="stat-card stat-blue">
                <div class="stat-icon">📚</div>
                <div class="stat-value"><?= $totalClasses ?></div>
                <div class="stat-label">Classes</div>
            </div>
            <div class="stat-card stat-amber">
                <div class="stat-icon">⏰</div>
                <div class="stat-value">
                    <?= $nextClass ? date('g:i A', strtotime($nextClass['start_time'])) : '—' ?>
                </div>
                <div class="stat-label">Next Class</div>
            </div>
            <div class="stat-card stat-green">
                <div class="stat-icon">🕒</div>
                <div class="stat-value" style="font-size: 1rem;">
                    <?= !empty($currentStatus['updated_at']) ? date('g:i A', strtotime($currentStatus['updated_at'])) : 'Never' ?>
                </div>
                <div class="stat-label">Last Updated</div>
            </div>
        </div>

        <div class="grid">

            <!-- Location & Status -->
            <div class="panel">
                <h3>Current Location & Status</h3>

                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                    <div class="setting">
                        <label for="location">Current Location</label>
                        <input type="text" id="location" name="location"
                               value="<?= htmlspecialchars($currentStatus['location'] ?? '') ?>"
                               placeholder="Example: Room 204, ICT Laboratory" required>
                    </div>

                    <div class="setting">
                        <label for="status">Current Status</label>
                        <select id="status" name="status">
                            <?php
                            $statuses = [
                                'available' => 'Available', 'in_class' => 'In Class', 'busy' => 'Busy',
                                'in_meeting' => 'In a Meeting', 'consultation_hours' => 'Consultation Hours',
                                'out_of_campus' => 'Out of Campus'
                            ];
                            foreach ($statuses as $val => $label): ?>
                                <option value="<?= $val ?>" <?= ($currentStatus['status'] ?? '') === $val ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="submit" name="update_status" value="1" class="save-btn">Save Status</button>
                </form>

                <form method="POST" id="autoUpdateForm">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
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

            <!-- Personal Note -->
            <div class="panel">
                <h3>Personal Note</h3>
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <textarea name="note" placeholder="Write a personal note..."><?= htmlspecialchars($currentStatus['note'] ?? '') ?></textarea>
                    <div class="note-buttons">
                        <button type="submit" name="update_note" value="1" class="save-btn">Save Note</button>
                    </div>
                </form>
            </div>

            <!-- Class Schedule -->
            <div class="panel schedule-container">
                <div class="schedule-header">
                    <div>
                        <h3>Class Schedule</h3>
                        <p class="schedule-description">Weekly class schedule</p>
                    </div>
                    <div class="semester-control">
                        <a href="classes.php" class="edit-profile-btn">Manage Classes</a>
                    </div>
                </div>

                <div class="calendar-wrapper">
                    <div class="calendar">
                        <div class="time-column">
                            <div class="calendar-header-space"></div>
                            <?php for ($h = 7; $h <= 21; $h++):
                                $displayHour = $h % 12 === 0 ? 12 : $h % 12;
                                $ampm = $h < 12 ? 'AM' : 'PM';
                            ?>
                                <div class="time"><?= $displayHour ?>:00 <?= $ampm ?></div>
                            <?php endfor; ?>
                        </div>

                        <?php
                        $days = [1=>'Monday',2=>'Tuesday',3=>'Wednesday',4=>'Thursday',5=>'Friday',6=>'Saturday',7=>'Sunday'];
                        foreach ($days as $dayNum => $dayName): ?>
                            <div class="day-column">
                                <div class="day-header"><?= $dayName ?></div>
                                <div class="calendar-body">
                                    <?php foreach ($classes as $class): ?>
                                        <?php if (($class['day_of_week'] ?? 0) == $dayNum):
                                            $startTime = strtotime($class['start_time']);
                                            $endTime = strtotime($class['end_time']);
                                            $startHour = (int)date('H', $startTime);
                                            $startMin = (int)date('i', $startTime);
                                            $top = (($startHour - 7) * 60) + $startMin;
                                            $durationMinutes = ($endTime - $startTime) / 60;
                                        ?>
                                            <div class="class-block" style="top: <?= $top ?>px; height: <?= $durationMinutes ?>px;">
                                                <strong><?= htmlspecialchars($class['subject'] ?? '') ?></strong>
                                                <span><?= date('g:i A', $startTime) ?> - <?= date('g:i A', $endTime) ?></span>
                                                <span><?= htmlspecialchars($class['room'] ?? '') ?></span>
                                                <span><?= htmlspecialchars($class['section'] ?? '') ?></span>
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
</div>

<?php if ($successMessage): ?>
<script>alert("<?= htmlspecialchars($successMessage, ENT_QUOTES) ?>");</script>
<?php endif; ?>
<?php if ($errorMessage): ?>
<script>alert("<?= htmlspecialchars($errorMessage, ENT_QUOTES) ?>");</script>
<?php endif; ?>

</body>
</html>