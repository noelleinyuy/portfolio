<?php

session_start();
require_once "db.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit;
}

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

$testimonialTotal = count_rows($conn, "SELECT COUNT(*) c FROM portfolio_testimonials");
$testimonialVisible = count_rows($conn, "SELECT COUNT(*) c FROM portfolio_testimonials WHERE Status='Visible'");
$messageTotal = count_rows($conn, "SELECT COUNT(*) c FROM portfolio_messages");
$serviceTotal = count_rows($conn, "SELECT COUNT(*) c FROM portfolio_services");
$projectTotal = count_rows($conn, "SELECT COUNT(*) c FROM portfolio_projects");

$hasIsRead = $conn->query("SHOW COLUMNS FROM portfolio_messages LIKE 'IsRead'")->num_rows > 0;
$hasMessageId = $conn->query("SHOW COLUMNS FROM portfolio_messages LIKE 'MessageID'")->num_rows > 0;

$messageUnread = $hasIsRead
    ? count_rows($conn, "SELECT COUNT(*) c FROM portfolio_messages WHERE IsRead = 0")
    : 0;

$orderCol = $hasMessageId ? "MessageID" : "1";
$recentMessages = $conn->query("SELECT * FROM portfolio_messages ORDER BY $orderCol DESC LIMIT 5");

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard</title>

    <link href="css/boxicons.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/admin.css">

</head>

<body>

    <div class="admin-shell">

        <aside class="admin-sidebar" id="admin-sidebar">

            <div class="admin-logo">Noel<span>.admin</span></div>

            <nav class="admin-nav">
                <a href="admin_dashboard.php" class="active"><i class="bx bx-grid-alt"></i> Dashboard</a>
                <a href="admin_testimonials.php"><i class="bx bx-chat"></i> Testimonials</a>
                <a href="admin_messages.php"><i class="bx bx-envelope"></i> Messages</a>
                <a href="admin_services.php"><i class="bx bx-briefcase"></i> Services</a>
                <a href="admin_projects.php"><i class="bx bx-code-alt"></i> Projects</a>
                <a href="admin_visitors.php"><i class="bx bx-line-chart"></i> Visitors</a>
                <a href="admin_settings.php"><i class="bx bx-cog"></i> Settings</a>
            </nav>

            <a href="admin_logout.php" class="admin-logout"><i class="bx bx-log-out"></i> Logout</a>

        </aside>

        <div class="admin-main">

            <header class="admin-topbar">
                <button id="sidebar-toggle" class="sidebar-toggle" aria-label="Toggle menu">
                    <i class="bx bx-menu"></i>
                </button>
                <h1>Dashboard</h1>
                <div class="admin-user">
                    <i class="bx bx-user-circle"></i>
                    <?php echo htmlspecialchars($adminName); ?>
                </div>
            </header>

            <main class="admin-content">

                <div class="stat-grid">

                    <div class="stat-card">
                        <i class="bx bx-chat"></i>
                        <div>
                            <span><?php echo $testimonialTotal; ?></span>
                            <p><?php echo $testimonialVisible; ?> visible testimonials</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <i class="bx bx-envelope"></i>
                        <div>
                            <span><?php echo $messageTotal; ?></span>
                            <p><?php echo $messageUnread; ?> unread messages</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <i class="bx bx-briefcase"></i>
                        <div>
                            <span><?php echo $serviceTotal; ?></span>
                            <p>Services listed</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <i class="bx bx-code-alt"></i>
                        <div>
                            <span><?php echo $projectTotal; ?></span>
                            <p>Projects listed</p>
                        </div>
                    </div>

                </div>

                <div class="admin-panel">
                    <h2>Recent messages</h2>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Subject</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($recentMessages && $recentMessages->num_rows > 0): ?>
                                <?php while ($m = $recentMessages->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($m["FullName"]); ?></td>
                                        <td><?php echo htmlspecialchars($m["Email"]); ?></td>
                                        <td><?php echo htmlspecialchars($m["Subject"] ?: "—"); ?></td>
                                        <td><a href="admin_messages.php" class="btn-link">View all →</a></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4">No messages yet.</td>
                                </tr>
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