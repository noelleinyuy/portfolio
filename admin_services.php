<?php

require_once "admin_helpers.php";
require_once "db.php";
require_admin_login();

$adminName = $_SESSION["admin_name"] ?? "Admin";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $action = $_POST["action"] ?? "";
    $id = (int) ($_POST["id"] ?? 0);

    if ($action === "toggle" && $id) {

        $conn->query("UPDATE portfolio_services SET Status = IF(Status = 'Visible', 'Hidden', 'Visible') WHERE ServiceID = $id");

    } elseif ($action === "delete" && $id) {

        $conn->query("DELETE FROM portfolio_services WHERE ServiceID = $id");
        flash("success", "Service deleted.");

    } elseif ($action === "add" || $action === "update") {

        $title = trim($_POST["title"] ?? "");
        $description = trim($_POST["description"] ?? "");
        $icon = trim($_POST["icon"] ?? "bx bx-code");

        if ($title === "" || $description === "") {

            flash("error", "Title and description are required.");

        } elseif ($action === "add") {

            $stmt = $conn->prepare("
                INSERT INTO portfolio_services (Title, Description, Icon, Status)
                VALUES (?, ?, ?, 'Visible')
            ");
            $stmt->bind_param("sss", $title, $description, $icon);
            $stmt->execute();
            $stmt->close();
            flash("success", "Service added.");

        } else {

            $stmt = $conn->prepare("
                UPDATE portfolio_services
                SET Title = ?, Description = ?, Icon = ?
                WHERE ServiceID = ?
            ");
            $stmt->bind_param("sssi", $title, $description, $icon, $id);
            $stmt->execute();
            $stmt->close();
            flash("success", "Service updated.");

        }
    }

    header("Location: admin_services.php");
    exit;
}

$editId = (int) ($_GET["edit"] ?? 0);
$editing = null;

if ($editId) {
    $stmt = $conn->prepare("SELECT * FROM portfolio_services WHERE ServiceID = ?");
    $stmt->bind_param("i", $editId);
    $stmt->execute();
    $editing = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$services = $conn->query("SELECT * FROM portfolio_services ORDER BY ServiceID DESC");

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Services — Admin</title>

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
            <a href="admin_services.php" class="active"><i class="bx bx-briefcase"></i> Services</a>
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
            <h1>Services</h1>
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
                <h2><?php echo $editing ? "Edit service" : "Add a service"; ?></h2>

                <?php if (!$editing): ?>
                    <p class="muted" style="margin-bottom: 1.2rem;">
                        Icon uses a Boxicons class name, e.g. <code>bx bx-code</code>, <code>bx bx-palette</code>,
                        <code>bx bxl-android</code>. Browse more at boxicons.com.
                    </p>
                <?php endif; ?>

                <form method="POST" class="admin-form">
                    <input type="hidden" name="action" value="<?php echo $editing ? "update" : "add"; ?>">
                    <?php if ($editing): ?>
                        <input type="hidden" name="id" value="<?php echo (int) $editing["ServiceID"]; ?>">
                    <?php endif; ?>

                    <div class="form-row">
                        <div>
                            <label>Title</label>
                            <input type="text" name="title" required value="<?php echo e($editing["Title"] ?? ""); ?>">
                        </div>
                        <div>
                            <label>Icon (Boxicons class)</label>
                            <input type="text" name="icon" value="<?php echo e($editing["Icon"] ?? "bx bx-code"); ?>">
                        </div>
                    </div>

                    <label>Description</label>
                    <textarea name="description" rows="3" required><?php echo e($editing["Description"] ?? ""); ?></textarea>

                    <div class="row-actions">
                        <button type="submit" class="btn-primary"><?php echo $editing ? "Save changes" : "Add service"; ?></button>
                        <?php if ($editing): ?>
                            <a href="admin_services.php" class="btn-small">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <div class="admin-panel">
                <h2>All services</h2>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Icon</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Added</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($services && $services->num_rows > 0): ?>
                            <?php while ($s = $services->fetch_assoc()): ?>
                                <tr>
                                    <td><i class="<?php echo e($s["Icon"] ?: "bx bx-code"); ?>" style="font-size: 2rem; color: var(--main-color);"></i></td>
                                    <td><?php echo e($s["Title"]); ?></td>
                                    <td class="truncate"><?php echo e($s["Description"]); ?></td>
                                    <td>
                                        <span class="badge <?php echo $s["Status"] === "Visible" ? "badge-green" : "badge-gray"; ?>">
                                            <?php echo e($s["Status"]); ?>
                                        </span>
                                    </td>
                                    <td class="muted"><?php echo e(date("M j, Y", strtotime($s["CreatedAt"]))); ?></td>
                                    <td class="row-actions">
                                        <a href="admin_services.php?edit=<?php echo (int) $s["ServiceID"]; ?>" class="btn-small">Edit</a>

                                        <form method="POST">
                                            <input type="hidden" name="action" value="toggle">
                                            <input type="hidden" name="id" value="<?php echo (int) $s["ServiceID"]; ?>">
                                            <button type="submit" class="btn-small"><?php echo $s["Status"] === "Visible" ? "Hide" : "Show"; ?></button>
                                        </form>

                                        <form method="POST" onsubmit="return confirm('Delete this service?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo (int) $s["ServiceID"]; ?>">
                                            <button type="submit" class="btn-small btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6">No services yet.</td></tr>
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