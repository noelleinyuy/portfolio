<?php

require_once "admin_helpers.php";
require_once "db.php";
require_admin_login();

$adminName = $_SESSION["admin_name"] ?? "Admin";

function analytics_count(mysqli $conn, string $sql): int
{
    $result = @$conn->query($sql);
    if (!$result) {
        return 0;
    }

    $row = $result->fetch_assoc();
    return (int) ($row["count"] ?? 0);
}

$totalVisits = analytics_count($conn, "SELECT COUNT(*) AS count FROM portfolio_visits");
$uniqueVisitors = analytics_count($conn, "SELECT COUNT(DISTINCT SessionID) AS count FROM portfolio_visits");
$visitsToday = analytics_count($conn, "SELECT COUNT(*) AS count FROM portfolio_visits WHERE DATE(VisitedAt) = CURDATE()");
$totalActions = analytics_count($conn, "SELECT COUNT(*) AS count FROM portfolio_visitor_actions");

$topPages = $conn->query("
    SELECT PageURL, COUNT(*) AS Visits
    FROM portfolio_visits
    GROUP BY PageURL
    ORDER BY Visits DESC
    LIMIT 10
");

$dailyVisits = $conn->query("
    SELECT DATE(VisitedAt) AS VisitDate, COUNT(*) AS Visits
    FROM portfolio_visits
    WHERE VisitedAt >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
    GROUP BY DATE(VisitedAt)
    ORDER BY VisitDate ASC
");

$dailyData = [];
if ($dailyVisits) {
    while ($day = $dailyVisits->fetch_assoc()) {
        $dailyData[$day["VisitDate"]] = (int) $day["Visits"];
    }
}

$maxDailyVisits = max(1, ...array_values($dailyData));

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics — Admin</title>

    <link rel="stylesheet" href="css/boxicons.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/admin.css">

</head>

<body>

<div class="admin-shell">

    <aside class="admin-sidebar" id="admin-sidebar">

        <div class="admin-logo">Noel<span>.admin</span></div>

        <nav class="admin-nav">
            <a href="admin_dashboard.php"><i class="bx bx-grid-alt"></i> Dashboard</a>
            <a href="admin_analytics.php" class="active"><i class="bx bx-line-chart"></i> Analytics</a>
            <a href="admin_testimonials.php"><i class="bx bx-chat"></i> Testimonials</a>
            <a href="admin_messages.php"><i class="bx bx-envelope"></i> Messages</a>
            <a href="admin_services.php"><i class="bx bx-briefcase"></i> Services</a>
            <a href="admin_projects.php"><i class="bx bx-code-alt"></i> Projects</a>
            <a href="admin_readme.php"><i class="bx bx-file"></i> Read Me</a>
            <a href="admin_settings.php"><i class="bx bx-cog"></i> Settings</a>
        </nav>

        <a href="admin_logout.php" class="admin-logout"><i class="bx bx-log-out"></i> Logout</a>

    </aside>

    <div class="admin-main">

        <header class="admin-topbar">
            <button id="sidebar-toggle" class="sidebar-toggle" aria-label="Toggle menu">
                <i class="bx bx-menu"></i>
            </button>
            <h1>Analytics</h1>
            <div class="admin-user">
                <i class="bx bx-user-circle"></i>
                <?php echo e($adminName); ?>
            </div>
        </header>

        <main class="admin-content">

            <div class="stat-grid">
                <div class="stat-card">
                    <i class="bx bx-show"></i>
                    <div><span><?php echo $totalVisits; ?></span><p>Total page visits</p></div>
                </div>
                <div class="stat-card">
                    <i class="bx bx-user"></i>
                    <div><span><?php echo $uniqueVisitors; ?></span><p>Unique visitors</p></div>
                </div>
                <div class="stat-card">
                    <i class="bx bx-calendar-check"></i>
                    <div><span><?php echo $visitsToday; ?></span><p>Visits today</p></div>
                </div>
                <div class="stat-card">
                    <i class="bx bx-bolt"></i>
                    <div><span><?php echo $totalActions; ?></span><p>Tracked actions</p></div>
                </div>
            </div>

            <div class="admin-panel">
                <h2>Visits in the last 7 days</h2>
                <div class="chart-bars">
                    <?php for ($offset = 6; $offset >= 0; $offset--): ?>
                        <?php
                        $date = date("Y-m-d", strtotime("-$offset days"));
                        $visits = $dailyData[$date] ?? 0;
                        $height = max(2, (int) round(($visits / $maxDailyVisits) * 100));
                        ?>
                        <div class="chart-col">
                            <span class="chart-count"><?php echo $visits; ?></span>
                            <div class="chart-bar" style="height: <?php echo $height; ?>%;"></div>
                            <span class="chart-label"><?php echo e(date("M j", strtotime($date))); ?></span>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>

            <div class="admin-panel">
                <h2>Most visited pages</h2>
                <table class="admin-table admin-responsive-table">
                    <thead>
                        <tr>
                            <th>Page</th>
                            <th>Visits</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($topPages && $topPages->num_rows > 0): ?>
                            <?php while ($page = $topPages->fetch_assoc()): ?>
                                <tr>
                                    <td data-label="Page"><?php echo e($page["PageURL"]); ?></td>
                                    <td data-label="Visits"><?php echo (int) $page["Visits"]; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="2">No visits recorded yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </main>

    </div>

</div>

<script src="js/admin.js"></script>

</body>

</html>
