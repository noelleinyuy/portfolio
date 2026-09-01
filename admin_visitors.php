<?php

require_once "admin_helpers.php";
require_once "db.php";
require_admin_login();

$adminName = $_SESSION["admin_name"] ?? "Admin";

function count_rows(mysqli $conn, string $sql): int
{
    $result = @$conn->query($sql);
    if (!$result) {
        return 0;
    }
    $row = $result->fetch_assoc();
    return (int) ($row["c"] ?? 0);
}

$totalVisits = count_rows($conn, "SELECT COUNT(*) c FROM portfolio_visits");
$uniqueVisitors = count_rows($conn, "SELECT COUNT(DISTINCT SessionID) c FROM portfolio_visits");
$totalActions = count_rows($conn, "SELECT COUNT(*) c FROM portfolio_visitor_actions");
$visitsToday = count_rows($conn, "SELECT COUNT(*) c FROM portfolio_visits WHERE DATE(VisitedAt) = CURDATE()");

$topPages = $conn->query("
    SELECT PageURL, COUNT(*) AS Visits
    FROM portfolio_visits
    GROUP BY PageURL
    ORDER BY Visits DESC
    LIMIT 5
");

$recentVisits = $conn->query("
    SELECT * FROM portfolio_visits
    ORDER BY VisitID DESC
    LIMIT 15
");

$recentActions = $conn->query("
    SELECT * FROM portfolio_visitor_actions
    ORDER BY ActionID DESC
    LIMIT 15
");

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Visitors — Admin</title>

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
            <a href="admin_testimonials.php"><i class="bx bx-chat"></i> Testimonials</a>
            <a href="admin_messages.php"><i class="bx bx-envelope"></i> Messages</a>
            <a href="admin_services.php"><i class="bx bx-briefcase"></i> Services</a>
            <a href="admin_projects.php"><i class="bx bx-code-alt"></i> Projects</a>
            <a href="admin_visitors.php" class="active"><i class="bx bx-line-chart"></i> Visitors</a>
            <a href="admin_settings.php"><i class="bx bx-cog"></i> Settings</a>
        </nav>

        <a href="admin_logout.php" class="admin-logout"><i class="bx bx-log-out"></i> Logout</a>

    </aside>

    <div class="admin-main">

        <header class="admin-topbar">
            <button id="sidebar-toggle" class="sidebar-toggle" aria-label="Toggle menu">
                <i class="bx bx-menu"></i>
            </button>
            <h1>Visitors</h1>
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
                <h2>Top pages</h2>
                <table class="admin-table">
                    <thead><tr><th>Page</th><th>Visits</th></tr></thead>
                    <tbody>
                        <?php if ($topPages && $topPages->num_rows > 0): ?>
                            <?php while ($p = $topPages->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo e($p["PageURL"]); ?></td>
                                    <td><?php echo (int) $p["Visits"]; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="2">No visits recorded yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="admin-panel">
                <h2>Recent visits</h2>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Page</th>
                            <th>IP address</th>
                            <th>Referrer</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($recentVisits && $recentVisits->num_rows > 0): ?>
                            <?php while ($v = $recentVisits->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo e($v["PageURL"]); ?></td>
                                    <td class="muted"><?php echo e($v["IPAddress"]); ?></td>
                                    <td class="truncate muted"><?php echo e($v["Referrer"] ?: "Direct"); ?></td>
                                    <td class="muted"><?php echo e(date("M j, g:ia", strtotime($v["VisitedAt"]))); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4">No visits recorded yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="admin-panel">
                <h2>Recent actions</h2>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>Detail</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($recentActions && $recentActions->num_rows > 0): ?>
                            <?php while ($a = $recentActions->fetch_assoc()): ?>
                                <tr>
                                    <td><span class="badge badge-blue"><?php echo e($a["ActionType"]); ?></span></td>
                                    <td><?php echo e($a["ActionDetail"]); ?></td>
                                    <td class="muted"><?php echo e(date("M j, g:ia", strtotime($a["CreatedAt"]))); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="3">No actions recorded yet.</td></tr>
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