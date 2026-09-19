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
<link rel="stylesheet" href="style.css">
</head>
<body>
<h1>Faculty & Staff Availability</h1>
<form method="GET">
    <input type="text" name="search" placeholder="Search name or department"
        value="<?= htmlspecialchars($search) ?>">
    <button type="submit">Search</button>
</form>
<table>
<tr>
    <th><a href="?sort=id">ID</a></th>
    <th><a href="?sort=full_name">Name</a></th>
    <th><a href="?sort=department">Department</a></th>
    <th><a href="?sort=role_title">Role</a></th>
    <th><a href="?sort=status">Status</a></th>
    <th>Location</th>
</tr>
<?php foreach ($people as $person): ?>
<tr>
    <td><?= $person["id"] ?></td>
    <td><?= htmlspecialchars($person["full_name"]) ?></td>
    <td><?= htmlspecialchars($person["department"]) ?></td>
    <td><?= htmlspecialchars($person["role_title"]) ?></td>
    <td><?= statusLabel($person["status"]) ?></td>
    <td><?= htmlspecialchars($person["location"] ?? "-") ?></td>
</tr>
<?php endforeach; ?>
<?php if (empty($people)): ?>
<tr><td colspan="6">No faculty or staff found.</td></tr>
<?php endif; ?>
</table>
</body>
</html>