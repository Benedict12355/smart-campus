<?php
require_once "../config/db.php";
function statusLabel($status) {
    $labels = [
        "available" => "Available",
        "in_class" => "In Class",
        "busy" => "Busy",
        "in_meeting" => "In Meeting",
        "consultation_hours" => "Consultation Hours",
        "out_of_campus" => "Out of Campus",
    ];
    return $labels[$status] ?? "No status set";
}
function statusClass($status) {
    $classes = [
        "available" => "status-available",
        "consultation_hours" => "status-available",
        "in_class" => "status-busy",
        "busy" => "status-busy",
        "in_meeting" => "status-busy",
        "out_of_campus" => "status-away",
    ];
    return $classes[$status] ?? "status-away";
}


$search = $_GET["search"] ?? "";
$sort = $_GET["sort"] ?? "id";
$allowedSorts = ["id", "full_name", "department", "role_title", "status"];
if (!in_array($sort, $allowedSorts)) {
    $sort = "id";
}
$sql = "SELECT f.id, f.full_name, f.department, f.role_title,
               fs.status, fs.location, fs.updated_at
        FROM faculty_staff f
        LEFT JOIN faculty_status fs ON fs.faculty_id = f.id";
if ($search !== "") {
    $sql .= " WHERE f.full_name LIKE :search OR f.department LIKE :search";
    $sql .= " ORDER BY $sort DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["search" => "%$search%"]);
} else {
    $sql .= " ORDER BY $sort DESC";
    $stmt = $pdo->query($sql);
}
$people = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Faculty & Staff Availability</title>
<link rel="stylesheet" href="../assets/publicstyle.css">
</head>
<body>
<h1>Faculty & Staff Availability</h1>
<form method="GET">
    <input type="text" name="search" placeholder="Search name or department"
        value="<?= htmlspecialchars($search) ?>">
    <button type="submit">Search</button>
</form>
<div class="toolbar">
    <a href="?sort=full_name">Sort by Name</a>
    <a href="?sort=department">Sort by Department</a>
    <a href="?sort=status">Sort by Status</a>
</div>

<div class="card-grid">
<?php foreach ($people as $person): ?>
    <div class="card <?= statusClass($person["status"]) ?>">
        <h2><?= htmlspecialchars($person["full_name"]) ?></h2>
        <p class="meta"><?= htmlspecialchars($person["role_title"]) ?> &middot; <?= htmlspecialchars($person["department"]) ?></p>
        <p class="status-badge"><?= statusLabel($person["status"]) ?></p>
        <?php if ($person["location"]): ?>
            <p class="location">📍 <?= htmlspecialchars($person["location"]) ?></p>
        <?php endif; ?>
    </div>
<?php endforeach; ?>

<?php if (empty($people)): ?>
    <p>No faculty or staff found.</p>
<?php endif; ?>
</div>
</body>
</html>