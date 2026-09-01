<?php

require_once "admin_helpers.php";
require_once "db.php";
require_admin_login();

$adminName = $_SESSION["admin_name"] ?? "Admin";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $action = $_POST["action"] ?? "";
    $id = (int) ($_POST["id"] ?? 0);

    if ($action === "mark_read" && $id) {

        $conn->query("UPDATE portfolio_messages SET Status = 'Read' WHERE MessageID = $id AND Status = 'Unread'");

    } elseif ($action === "reply" && $id) {

        $replyText = trim($_POST["reply"] ?? "");

        $stmt = $conn->prepare("
            UPDATE portfolio_messages
            SET Reply = ?, RepliedAt = NOW(), Status = 'Replied'
            WHERE MessageID = ?
        ");
        $stmt->bind_param("si", $replyText, $id);
        $stmt->execute();
        $stmt->close();

        flash("success", "Reply saved and message marked as replied.");

    } elseif ($action === "delete" && $id) {

        $conn->query("DELETE FROM portfolio_messages WHERE MessageID = $id");
        flash("success", "Message deleted.");

    }

    header("Location: admin_messages.php");
    exit;
}

$messages = $conn->query("SELECT * FROM portfolio_messages ORDER BY MessageID DESC");

$statusBadge = [
    "Unread" => "badge-gray",
    "Read" => "badge-blue",
    "Replied" => "badge-green",
];

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Messages — Admin</title>

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
            <a href="admin_messages.php" class="active"><i class="bx bx-envelope"></i> Messages</a>
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
            <h1>Messages</h1>
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
                <h2>Contact messages</h2>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>From</th>
                            <th>Phone</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Received</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($messages && $messages->num_rows > 0): ?>
                            <?php while ($m = $messages->fetch_assoc()): ?>
                                <?php
                                $badgeClass = $statusBadge[$m["Status"]] ?? "badge-gray";
                                $mailtoSubject = rawurlencode("Re: " . ($m["Subject"] ?: "your message"));
                                $mailtoBody = rawurlencode($m["Reply"] ?: ("Hi " . $m["FullName"] . ",\n\n"));
                                ?>
                                <tr class="<?php echo $m["Status"] === "Unread" ? "row-unread" : ""; ?>">
                                    <td>
                                        <?php echo e($m["FullName"]); ?><br>
                                        <span class="muted"><?php echo e($m["Email"]); ?></span>
                                    </td>
                                    <td><?php echo e($m["Phone"]); ?></td>
                                    <td><?php echo e($m["Subject"]); ?></td>
                                    <td class="truncate"><?php echo e($m["Message"]); ?></td>
                                    <td>
                                        <span class="badge <?php echo $badgeClass; ?>"><?php echo e($m["Status"]); ?></span>
                                    </td>
                                    <td class="muted"><?php echo e(date("M j, g:ia", strtotime($m["CreatedAt"]))); ?></td>
                                    <td class="row-actions">

                                        <?php if ($m["Status"] === "Unread"): ?>
                                            <form method="POST">
                                                <input type="hidden" name="action" value="mark_read">
                                                <input type="hidden" name="id" value="<?php echo (int) $m["MessageID"]; ?>">
                                                <button type="submit" class="btn-small">Mark read</button>
                                            </form>
                                        <?php endif; ?>

                                        <details>
                                            <summary class="btn-small">Reply</summary>
                                            <form method="POST" class="admin-form reply-form">
                                                <input type="hidden" name="action" value="reply">
                                                <input type="hidden" name="id" value="<?php echo (int) $m["MessageID"]; ?>">
                                                <textarea name="reply" rows="3" placeholder="Type your reply..."><?php echo e($m["Reply"] ?? ""); ?></textarea>
                                                <div class="row-actions">
                                                    <button type="submit" class="btn-small">Save &amp; mark replied</button>
                                                    <a
                                                        class="btn-small"
                                                        href="mailto:<?php echo e($m["Email"]); ?>?subject=<?php echo $mailtoSubject; ?>&body=<?php echo $mailtoBody; ?>"
                                                    >Open in email app</a>
                                                </div>
                                            </form>
                                        </details>

                                        <form method="POST" onsubmit="return confirm('Delete this message?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo (int) $m["MessageID"]; ?>">
                                            <button type="submit" class="btn-small btn-danger">Delete</button>
                                        </form>

                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="7">No messages yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </main>

    </div>

</div>

<script src="js/admin.js"></script>

</body>

</html>F