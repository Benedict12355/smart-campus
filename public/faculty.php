<?php
require_once "../config/db.php";
function statusLabel($status)
{
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
    if ($status === null) return "status-none";
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
function statusBadgeClass($status)
{
    $classes = [
        "available" => "bg-success",
        "consultation_hours" => "bg-success",
        "in_class" => "bg-warning text-dark",
        "busy" => "bg-warning text-dark",
        "in_meeting" => "bg-warning text-dark",
        "out_of_campus" => "bg-secondary",
    ];
    return $classes[$status] ?? "bg-secondary";
}
function statusIconPath($status) {
    if ($status === null) return "../assets/image/no_status.png";
    $icons = [
        "available" => "../assets/image/available.png",
        "consultation_hours" => "../assets/image/consulting_hours.png",
        "in_class" => "../assets/image/in_class.png",
        "busy" => "../assets/image/unavailable.png",
        "in_meeting" => "../assets/image/meeting.png",
        "out_of_campus" => "../assets/image/out_of_campus.png",
    ];
    return $icons[$status] ?? "../assets/image/available.png";
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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty & Staff Availability</title>
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/publicstyle.css">
</head>

<body>

    <nav class="navbar navbar-dark bg-primary mb-4">
        <div class="container">
            <span class="navbar-brand mb-0 h1">Faculty &amp; Staff Availability</span>
        </div>
    </nav>

    <div class="container">

        <form method="GET" class="row g-2 justify-content-center mb-3">
            <div class="col-auto">
                <input type="text" name="search" class="form-control" placeholder="Search name or department"
                    value="<?= htmlspecialchars($search) ?>">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </form>

        <div class="d-flex justify-content-center gap-3 mb-4 flex-wrap">
            <a href="?sort=full_name" class="link-primary text-decoration-none">Sort by Name</a>
            <a href="?sort=department" class="link-primary text-decoration-none">Sort by Department</a>
            <a href="?sort=status" class="link-primary text-decoration-none">Sort by Status</a>
        </div>

        <div class="row g-4">
            <?php foreach ($people as $i => $person): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm fade-card <?= statusClass($person["status"]) ?>"
                        style="--delay: <?= $i * 0.08 ?>s">
                        <div class="card-body d-flex gap-3 align-items-start">
                            <div class="status-icon">
                                <img src="<?= statusIconPath($person["status"]) ?>"
                                    alt="<?= statusLabel($person["status"]) ?>">
                            </div>
                            <div>
                                <h5 class="card-title mb-1"><?= htmlspecialchars($person["full_name"]) ?></h5>
                                <p class="card-subtitle text-muted small mb-2">
                                    <?= htmlspecialchars($person["role_title"]) ?> &middot;
                                    <?= htmlspecialchars($person["department"]) ?>
                                </p>
                                <span class="badge mb-2">
                                    <?= statusLabel($person["status"]) ?>
                                </span>
                                <?php if ($person["location"]): ?>
                                    <p class="mb-0 small text-secondary">📍 <?= htmlspecialchars($person["location"]) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (empty($people)): ?>
                <div class="col-12">
                    <p class="text-center text-muted">No faculty or staff found.</p>
                </div>
            <?php endif; ?>
        </div>

    </div>

    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/kiosk.js"></script>
</body>

</html>