<?php

require_once "admin_helpers.php";
require_once "db.php";
require_admin_login();

$adminName = $_SESSION["admin_name"] ?? "Admin";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $heading = trim($_POST["heading"] ?? "");
    $content = trim($_POST["content"] ?? "");

    if ($heading === "" || $content === "") {

        flash("error", "Heading and content are required.");

    } else {

        $existing = $conn->query("SELECT ReadmeID FROM portfolio_readme ORDER BY ReadmeID ASC LIMIT 1")->fetch_assoc();

        if ($existing) {
            $stmt = $conn->prepare("UPDATE portfolio_readme SET Heading = ?, Content = ? WHERE ReadmeID = ?");
            $stmt->bind_param("ssi", $heading, $content, $existing["ReadmeID"]);
        } else {
            $stmt = $conn->prepare("INSERT INTO portfolio_readme (Heading, Content) VALUES (?, ?)");
            $stmt->bind_param("ss", $heading, $content);
        }

        $stmt->execute();
        $stmt->close();
        flash("success", "Read More page updated.");
    }

    header("Location: admin_readme.php");
    exit;
}

$heading = "";
$content = "";

$row = $conn->query("SELECT Heading, Content FROM portfolio_readme ORDER BY ReadmeID ASC LIMIT 1")->fetch_assoc();
if ($row) {
    $heading = $row["Heading"];
    $content = $row["Content"];
}

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Read Me — Admin</title>

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
            <a href="admin_analytics.php"><i class="bx bx-line-chart"></i> Analytics</a>
            <a href="admin_testimonials.php"><i class="bx bx-chat"></i> Testimonials</a>
            <a href="admin_messages.php"><i class="bx bx-envelope"></i> Messages</a>
            <a href="admin_services.php"><i class="bx bx-briefcase"></i> Services</a>
            <a href="admin_projects.php"><i class="bx bx-code-alt"></i> Projects</a>
            <a href="admin_readme.php" class="active"><i class="bx bx-file"></i> Read Me</a>
            <a href="admin_settings.php"><i class="bx bx-cog"></i> Settings</a>
        </nav>

        <a href="admin_logout.php" class="admin-logout"><i class="bx bx-log-out"></i> Logout</a>

    </aside>

    <div class="admin-main">

        <header class="admin-topbar">
            <button id="sidebar-toggle" class="sidebar-toggle" aria-label="Toggle menu">
                <i class="bx bx-menu"></i>
            </button>
            <h1>Read Me Page</h1>
            <div class="admin-user">
                <i class="bx bx-user-circle"></i>
                <?php echo e($adminName); ?>
            </div>
        </header>

        <main class="admin-content">

            <?php $f = get_flash(); if ($f): ?>
                <div class="admin-alert admin-alert-<?php echo e($f["type"]); ?>"><?php echo e($f["message"]); ?></div>
            <?php endif; ?>

            <div class="admin-panel">
                <h2>Edit "Read More" content</h2>
                <p class="muted" style="margin-bottom: 1.2rem;">
                    This is the full page visitors see when they click "Read more" on your About section.
                </p>

                <form method="POST" class="admin-form">
                    <label>Heading</label>
                    <input type="text" name="heading" required value="<?php echo e($heading); ?>">

                    <label>Content</label>
                    <textarea name="content" rows="12" required><?php echo e($content); ?></textarea>

                    <button type="submit" class="btn-primary">Save changes</button>
                    <a href="readme.php" target="_blank" class="btn-link" style="margin-left: 1rem;">Preview page ↗</a>
                </form>
            </div>

        </main>

    </div>

</div>

<script src="js/admin.js"></script>

</body>

</html>