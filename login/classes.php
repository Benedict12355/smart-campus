<?php
session_start();
require '../config/db.php';
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php');
    exit;
}

$facultyId = $_SESSION['faculty_staff_id'];
$days = [1=>'Monday',2=>'Tuesday',3=>'Wednesday',4=>'Thursday',5=>'Friday',6=>'Saturday',7=>'Sunday'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_class'])) {
    $section = trim($_POST['section']);
    $subject = trim($_POST['subject']);
    $dayOfWeek = $_POST['day_of_week'];
    $startTime = $_POST['start_time'];
    $endTime = $_POST['end_time'];
    $room = trim($_POST['room']);

    if (empty($section) || empty($subject) || empty($dayOfWeek) || empty($startTime) || empty($endTime) || empty($room)) {
        $message = 'Please fill in all fields.';
    } elseif ($startTime >= $endTime) {
        $message = 'End time must be later than start time.';
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO faculty_classes (faculty_id, section, subject, day_of_week, start_time, end_time, room)
            VALUES (:faculty_id, :section, :subject, :day_of_week, :start_time, :end_time, :room)
        ");
        $stmt->execute([
            ':faculty_id' => $facultyId, ':section' => $section, ':subject' => $subject,
            ':day_of_week' => $dayOfWeek, ':start_time' => $startTime, ':end_time' => $endTime, ':room' => $room
        ]);
        $_SESSION['success_message'] = 'Class has been added successfully.';
        header('Location: classes.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_class'])) {
    $classId = $_POST['class_id'];
    $section = trim($_POST['section']);
    $subject = trim($_POST['subject']);
    $dayOfWeek = $_POST['day_of_week'];
    $startTime = $_POST['start_time'];
    $endTime = $_POST['end_time'];
    $room = trim($_POST['room']);

    if (empty($section) || empty($subject) || empty($dayOfWeek) || empty($startTime) || empty($endTime) || empty($room)) {
        $message = 'Please fill in all fields.';
    } elseif ($startTime >= $endTime) {
        $message = 'End time must be later than start time.';
    } else {
        $stmt = $pdo->prepare("
            UPDATE faculty_classes
            SET section = :section, subject = :subject, day_of_week = :day_of_week, start_time = :start_time, end_time = :end_time, room = :room
            WHERE id = :id AND faculty_id = :faculty_id
        ");
        $stmt->execute([
            ':section' => $section, ':subject' => $subject, ':day_of_week' => $dayOfWeek,
            ':start_time' => $startTime, ':end_time' => $endTime, ':room' => $room,
            ':id' => $classId, ':faculty_id' => $facultyId
        ]);
        $_SESSION['success_message'] = 'Class has been updated successfully.';
        header('Location: classes.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_class'])) {
    $classId = $_POST['class_id'];
    $stmt = $pdo->prepare("DELETE FROM faculty_classes WHERE id = :id AND faculty_id = :faculty_id");
    $stmt->execute([':id' => $classId, ':faculty_id' => $facultyId]);
    $_SESSION['success_message'] = 'Class has been deleted.';
    header('Location: classes.php');
    exit;
}

$editClass = null;
if (isset($_GET['edit'])) {
    $editId = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM faculty_classes WHERE id = :id AND faculty_id = :faculty_id");
    $stmt->execute([':id' => $editId, ':faculty_id' => $facultyId]);
    $editClass = $stmt->fetch();
}

$stmt = $pdo->prepare("SELECT * FROM faculty_classes WHERE faculty_id = :faculty_id ORDER BY day_of_week, start_time");
$stmt->execute([':faculty_id' => $facultyId]);
$classes = $stmt->fetchAll();

$successMessage = $_SESSION['success_message'] ?? '';
unset($_SESSION['success_message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Classes</title>
    <link rel="stylesheet" href="../assets/style/shell.css">
    <link rel="stylesheet" href="../assets/style/class.css">
</head>
<body>

<div class="app-layout">

    <div class="sidebar">
        <div class="sidebar-brand">Faculty Portal</div>
        <a href="dashboard.php">📊 Dashboard</a>
        <a href="classes.php" class="active">📅 My Classes</a>
        <a href="edit_profile.php">👤 Edit Profile</a>
        <a href="process.php?logout=true" class="logout-link">🚪 Logout</a>
    </div>

    <div class="main-area">
        <div class="topbar">
            <div>
                <h1>My Classes</h1>
                <div class="topbar-sub">Manage your weekly class schedule</div>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <div class="panel">
            <h2><?= $editClass ? 'Edit Class' : '+ Add Class' ?></h2>
            <form method="POST">
                <?php if ($editClass): ?>
                    <input type="hidden" name="class_id" value="<?= $editClass['id'] ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label>Class / Section</label>
                    <input type="text" name="section" placeholder="Example: BSIS 3B" value="<?= htmlspecialchars($editClass['section'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label>Subject</label>
                    <input type="text" name="subject" placeholder="Example: Database Management" value="<?= htmlspecialchars($editClass['subject'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label>Day</label>
                    <select name="day_of_week" required>
                        <option value="">Select Day</option>
                        <?php foreach ($days as $dayNumber => $dayName): ?>
                            <option value="<?= $dayNumber ?>" <?= isset($editClass['day_of_week']) && $editClass['day_of_week'] == $dayNumber ? 'selected' : '' ?>><?= $dayName ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Room</label>
                    <input type="text" name="room" placeholder="Example: Comlab" value="<?= htmlspecialchars($editClass['room'] ?? '') ?>" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Start of Class</label>
                        <input type="time" name="start_time" value="<?= htmlspecialchars($editClass['start_time'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>End of Class</label>
                        <input type="time" name="end_time" value="<?= htmlspecialchars($editClass['end_time'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="button-row">
                    <?php if ($editClass): ?>
                        <button type="submit" name="update_class" value="1" class="add-btn">Update Class</button>
                        <a href="classes.php" class="cancel-btn">Cancel</a>
                    <?php else: ?>
                        <button type="submit" name="add_class" value="1" class="add-btn">Add Class</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="panel">
            <h2>My Class Schedule</h2>
            <?php if (count($classes) === 0): ?>
                <p>No classes have been added yet.</p>
            <?php else: ?>
                <div class="class-list">
                    <?php foreach ($classes as $class): ?>
                        <div class="class-item">
                            <h3><?= htmlspecialchars($class['subject']) ?></h3>
                            <div class="class-info">
                                <strong>Section:</strong> <?= htmlspecialchars($class['section']) ?><br>
                                <strong>Day:</strong> <?= htmlspecialchars($days[$class['day_of_week']] ?? 'Unknown') ?><br>
                                <strong>Time:</strong> <?= date('g:i A', strtotime($class['start_time'])) ?> - <?= date('g:i A', strtotime($class['end_time'])) ?><br>
                                <strong>Room:</strong> <?= htmlspecialchars($class['room']) ?>
                            </div>
                            <div class="class-actions">
                                <a href="classes.php?edit=<?= $class['id'] ?>" class="edit-btn" style="text-decoration:none; padding:10px 15px; border-radius:7px;">Edit</a>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this class schedule?');">
                                    <input type="hidden" name="class_id" value="<?= $class['id'] ?>">
                                    <button type="submit" name="delete_class" value="1" class="delete-btn">Delete</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if ($successMessage): ?>
<script>alert("<?= htmlspecialchars($successMessage, ENT_QUOTES) ?>");</script>
<?php endif; ?>

</body>
</html>