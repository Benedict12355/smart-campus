<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) { header('Location: index.php'); exit; }

$facultyId = $_SESSION['faculty_staff_id'] ?? null;
if (!$facultyId) { session_destroy(); header('Location: index.php'); exit; }

$statusLabels = [
    'available' => 'Available', 'in_class' => 'In Class', 'busy' => 'Busy',
    'in_meeting' => 'In a Meeting', 'consultation_hours' => 'Consultation Hours',
    'out_of_campus' => 'Out of Campus',
];

$allowedRanges = [7 => 'Last 7 days', 30 => 'Last 30 days', 90 => 'Last 90 days', 365 => 'Last year'];
$range = isset($_GET['range']) ? (int)$_GET['range'] : 30;
if (!isset($allowedRanges[$range])) $range = 30;

$hasLogs = false;
try { $hasLogs = $pdo->query("SHOW TABLES LIKE 'status_logs'")->rowCount() > 0; }
catch (PDOException $e) { $hasLogs = false; }

$statusCounts = []; $statusDurations = []; $dailyActivity = []; $rawLogs = []; $totalSeconds = 0;

if ($hasLogs) {
    $stmt = $pdo->prepare("SELECT status, location, logged_at FROM status_logs WHERE faculty_id = :fid AND logged_at >= DATE_SUB(NOW(), INTERVAL $range DAY) ORDER BY logged_at ASC");
    $stmt->execute([':fid' => $facultyId]);
    $rawLogs = $stmt->fetchAll();

    $now = time(); $count = count($rawLogs);
    for ($i = 0; $i < $count; $i++) {
        $start = strtotime($rawLogs[$i]['logged_at']);
        $end   = ($i + 1 < $count) ? strtotime($rawLogs[$i + 1]['logged_at']) : $now;
        $duration = max(0, $end - $start);
        $s = $rawLogs[$i]['status'];

        $statusCounts[$s]    = ($statusCounts[$s] ?? 0) + 1;
        $statusDurations[$s] = ($statusDurations[$s] ?? 0) + $duration;
        $totalSeconds += $duration;

        $day = date('Y-m-d', $start);
        $dailyActivity[$day] = ($dailyActivity[$day] ?? 0) + $duration;
    }
}

$dailyLabels = []; $today = new DateTime('today');
for ($i = $range - 1; $i >= 0; $i--) {
    $d = (clone $today)->modify("-$i days")->format('Y-m-d');
    $dailyLabels[] = $d;
    if (!isset($dailyActivity[$d])) $dailyActivity[$d] = 0;
}

arsort($statusDurations);

function formatDuration($seconds) {
    if ($seconds <= 0) return '0m';
    $h = floor($seconds / 3600); $m = floor(($seconds % 3600) / 60);
    return $h > 0 ? "{$h}h {$m}m" : "{$m}m";
}

$topStatus = null; $topDuration = 0;
foreach ($statusDurations as $s => $d) { if ($d > $topDuration) { $topDuration = $d; $topStatus = $s; } }
$totalSessions = array_sum($statusCounts);

$chartLabels = []; $chartDurations = []; $chartCounts = [];
foreach ($statusDurations as $s => $d) {
    $chartLabels[]    = $statusLabels[$s] ?? ucfirst(str_replace('_', ' ', $s));
    $chartDurations[] = round($d / 60, 1);
}
foreach ($statusCounts as $s => $c) $chartCounts[] = $c;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Analytics</title>
    <link rel="stylesheet" href="../assets/style/shell.css">
    <link rel="stylesheet" href="../assets/style/reports.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>
<body>
<div class="app-layout">
    <div class="sidebar">
        <div class="sidebar-brand">Faculty Portal</div>
        <a href="dashboard.php">📊 Dashboard</a>
        <a href="classes.php">📅 My Classes</a>
        <a href="reports.php" class="active">📈 Reports</a>
        <a href="edit_profile.php">👤 Edit Profile</a>
        <a href="process.php?logout=true" class="logout-link">🚪 Logout</a>
    </div>

    <div class="main-area">
        <div class="topbar">
            <div>
                <h1>Reports & Analytics</h1>
                <div class="topbar-sub">Your status activity over time</div>
            </div>
            <button id="exportPdfBtn" class="pdf-btn">⬇️ Export PDF</button>
        </div>

        <div class="range-tabs">
            <?php foreach ($allowedRanges as $r => $label): ?>
                <a href="?range=<?= $r ?>" class="range-tab <?= $r === $range ? 'active' : '' ?>"><?= $label ?></a>
            <?php endforeach; ?>
        </div>

        <?php if (!$hasLogs): ?>
            <div class="panel empty-state">
                <h3>Status logging is not set up yet</h3>
                <p>Please run <code>status_logs.sql</code> in phpMyAdmin to enable activity tracking.</p>
            </div>
        <?php elseif ($totalSessions === 0): ?>
            <div class="panel empty-state">
                <h3>No activity in this period</h3>
                <p>Change your status from the <a href="dashboard.php">dashboard</a> to start recording activity.</p>
            </div>
        <?php else: ?>

            <div id="reportArea">
                <div class="report-header">
                    <h2>Status Report — <?= htmlspecialchars($allowedRanges[$range]) ?></h2>
                    <p class="report-generated">Generated on <?= date('F j, Y \a\t g:i A') ?></p>
                </div>

                <div class="report-stats">
                    <div class="report-stat"><div class="report-stat-label">Total Tracked Time</div><div class="report-stat-value"><?= formatDuration($totalSeconds) ?></div></div>
                    <div class="report-stat"><div class="report-stat-label">Total Sessions</div><div class="report-stat-value"><?= $totalSessions ?></div></div>
                    <div class="report-stat"><div class="report-stat-label">Most Common Status</div><div class="report-stat-value small"><?= $topStatus ? htmlspecialchars($statusLabels[$topStatus] ?? $topStatus) : '—' ?></div></div>
                    <div class="report-stat"><div class="report-stat-label">Longest Time Spent</div><div class="report-stat-value small"><?= formatDuration($topDuration) ?></div></div>
                </div>

                <div class="chart-grid">
                    <div class="panel chart-panel">
                        <h3>Time Distribution</h3>
                        <div class="chart-wrapper"><canvas id="distChart"></canvas></div>
                    </div>
                    <div class="panel chart-panel">
                        <h3>Sessions per Status</h3>
                        <div class="chart-wrapper"><canvas id="countChart"></canvas></div>
                    </div>
                </div>

                <div class="panel chart-panel">
                    <h3>Daily Activity (minutes)</h3>
                    <div class="chart-wrapper tall"><canvas id="dailyChart"></canvas></div>
                </div>

                <div class="panel">
                    <h3>Breakdown</h3>
                    <table class="report-table">
                        <thead><tr><th>Status</th><th>Sessions</th><th>Total Time</th><th>% of Time</th></tr></thead>
                        <tbody>
                            <?php foreach ($statusDurations as $s => $dur):
                                $pct = $totalSeconds > 0 ? round(($dur / $totalSeconds) * 100, 1) : 0;
                            ?>
                                <tr>
                                    <td><?= htmlspecialchars($statusLabels[$s] ?? ucfirst(str_replace('_', ' ', $s))) ?></td>
                                    <td><?= $statusCounts[$s] ?? 0 ?></td>
                                    <td><?= formatDuration($dur) ?></td>
                                    <td><?= $pct ?>%</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="panel">
                    <h3>Recent Activity</h3>
                    <table class="report-table">
                        <thead><tr><th>When</th><th>Status</th><th>Location</th></tr></thead>
                        <tbody>
                            <?php $recent = array_slice(array_reverse($rawLogs), 0, 15); foreach ($recent as $log): ?>
                                <tr>
                                    <td><?= date('M j, Y g:i A', strtotime($log['logged_at'])) ?></td>
                                    <td><?= htmlspecialchars($statusLabels[$log['status']] ?? $log['status']) ?></td>
                                    <td><?= htmlspecialchars($log['location'] ?: '—') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if ($hasLogs && $totalSessions > 0): ?>
<script>
const palette = {
    'Available': '#22c55e', 'Consultation Hours': '#16a34a',
    'In Class': '#f59e0b', 'Busy': '#ef4444',
    'In a Meeting': '#f97316', 'Out of Campus': '#8b5cf6',
};
const labels      = <?= json_encode($chartLabels) ?>;
const durations   = <?= json_encode($chartDurations) ?>;
const counts      = <?= json_encode($chartCounts) ?>;
const dailyLabels = <?= json_encode($dailyLabels) ?>;
const dailyValues = <?= json_encode(array_values($dailyActivity)) ?>;
const colors      = labels.map(l => palette[l] || '#64748b');

Chart.defaults.color = '#c4b5fd';
Chart.defaults.font.family = 'Arial, sans-serif';

new Chart(document.getElementById('distChart'), {
    type: 'doughnut',
    data: { labels, datasets: [{ data: durations, backgroundColor: colors, borderColor: '#11101c', borderWidth: 2 }] },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { padding: 12, boxWidth: 12 } } } }
});

new Chart(document.getElementById('countChart'), {
    type: 'bar',
    data: { labels, datasets: [{ label: 'Sessions', data: counts, backgroundColor: colors, borderRadius: 6 }] },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#231a3d' } }, x: { grid: { display: false } } } }
});

new Chart(document.getElementById('dailyChart'), {
    type: 'line',
    data: { labels: dailyLabels, datasets: [{ label: 'Minutes active', data: dailyValues, borderColor: '#8b5cf6', backgroundColor: 'rgba(139, 92, 246, 0.15)', fill: true, tension: 0.3, pointRadius: 2 }] },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { color: '#231a3d' } }, x: { grid: { color: '#1a1530' } } } }
});

document.getElementById('exportPdfBtn').addEventListener('click', async () => {
    const btn = document.getElementById('exportPdfBtn');
    btn.disabled = true; btn.textContent = '⏳ Generating...';
    try {
        const canvas = await html2canvas(document.getElementById('reportArea'), { scale: 2, backgroundColor: '#080712', useCORS: true });
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF('p', 'mm', 'a4');
        const pageW = pdf.internal.pageSize.getWidth();
        const pageH = pdf.internal.pageSize.getHeight();
        const margin = 10;
        const usableW = pageW - margin * 2;
        const usableH = pageH - margin * 2;
        const imgW = usableW;
        const imgH = (canvas.height * imgW) / canvas.width;
        let heightLeft = imgH; let position = margin;
        const img = canvas.toDataURL('image/png');
        pdf.addImage(img, 'PNG', margin, position, imgW, imgH);
        heightLeft -= usableH;
        while (heightLeft > 0) {
            position = margin - (imgH - heightLeft);
            pdf.addPage();
            pdf.addImage(img, 'PNG', margin, position, imgW, imgH);
            heightLeft -= usableH;
        }
        pdf.save('status-report-<?= date('Y-m-d') ?>.pdf');
    } catch (err) {
        console.error(err); alert('Could not generate PDF.');
    } finally {
        btn.disabled = false; btn.textContent = '⬇️ Export PDF';
    }
});
</script>
<?php endif; ?>
</body>
</html>