<?php

require_once "admin_helpers.php";
require_once "db.php";
require_admin_login();

$adminName = $_SESSION["admin_name"] ?? "Admin";

$fields = [
    "SiteName" => "Site name / your name",
    "HomeIntro" => "Home intro paragraph",
    "TypedRoles" => "Typed roles (comma-separated)",
    "AboutHeading" => "About role/title (e.g. Software Engineering Student)",
    "AboutText" => "About paragraph",
    "ProfileImage" => "Profile image",
    "AboutImage" => "About image",
    "CVLink" => "CV download link",
    "SocialLinkedIn" => "LinkedIn URL",
    "SocialGithub" => "GitHub URL",
    "SocialFacebook" => "Facebook URL",
    "SocialInstagram" => "Instagram URL",
];

function upload_site_image(array $file, string $folderName): ?string
{
    if (!isset($file["tmp_name"]) || !is_uploaded_file($file["tmp_name"]) || $file["error"] !== UPLOAD_ERR_OK) {
        return null;
    }

    $mime = mime_content_type($file["tmp_name"]);
    $allowed = ["image/jpeg", "image/png", "image/gif", "image/webp"];
    if (!in_array($mime, $allowed, true)) {
        return null;
    }

    $folder = __DIR__ . "/images/" . $folderName . "/";
    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }

    $safeName = time() . "_" . preg_replace('/[^A-Za-z0-9._-]+/', '_', basename($file["name"]));
    $target = $folder . $safeName;

    if (!move_uploaded_file($file["tmp_name"], $target)) {
        return null;
    }

    return "images/" . $folderName . "/" . $safeName;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["form"] ?? "") === "site") {

    foreach ($fields as $key => $label) {
        $value = trim($_POST[$key] ?? "");

        if ($key === "ProfileImage" && isset($_FILES["ProfileImage"])) {
            $uploaded = upload_site_image($_FILES["ProfileImage"], "profile");
            if ($uploaded !== null) {
                $value = $uploaded;
            }
        } elseif ($key === "AboutImage" && isset($_FILES["AboutImage"])) {
            $uploaded = upload_site_image($_FILES["AboutImage"], "about");
            if ($uploaded !== null) {
                $value = $uploaded;
            }
        }

        set_setting($conn, $key, $value);
    }

    flash("success", "Settings saved.");
    header("Location: admin_settings.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["form"] ?? "") === "password") {

    $current = $_POST["current_password"] ?? "";
    $new = $_POST["new_password"] ?? "";
    $confirm = $_POST["confirm_password"] ?? "";

    $stmt = $conn->prepare("SELECT Password FROM portfolio_admin WHERE AdminID = ?");
    $stmt->bind_param("i", $_SESSION["admin_id"]);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$row || !password_verify($current, $row["Password"])) {
        flash("error", "Current password is incorrect.");
    } elseif (strlen($new) < 8) {
        flash("error", "New password must be at least 8 characters.");
    } elseif ($new !== $confirm) {
        flash("error", "New passwords do not match.");
    } else {
        $hash = password_hash($new, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE portfolio_admin SET Password = ? WHERE AdminID = ?");
        $stmt->bind_param("si", $hash, $_SESSION["admin_id"]);
        $stmt->execute();
        $stmt->close();
        flash("success", "Password updated.");
    }

    header("Location: admin_settings.php");
    exit;
}

$values = [];
foreach ($fields as $key => $label) {
    $values[$key] = get_setting($conn, $key);
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

    <title>Settings — Admin</title>

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
            <a href="admin_visitors.php"><i class="bx bx-line-chart"></i> Visitors</a>
            <a href="admin_settings.php" class="active"><i class="bx bx-cog"></i> Settings</a>
        </nav>

        <a href="admin_logout.php" class="admin-logout"><i class="bx bx-log-out"></i> Logout</a>

    </aside>

    <div class="admin-main">

        <header class="admin-topbar">
            <button id="sidebar-toggle" class="sidebar-toggle" aria-label="Toggle menu">
                <i class="bx bx-menu"></i>
            </button>
            <h1>Settings</h1>
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
                <h2>Site content</h2>
                <form method="POST" class="admin-form" enctype="multipart/form-data">
                    <input type="hidden" name="form" value="site">

                    <?php foreach ($fields as $key => $label): ?>
                        <label><?php echo e($label); ?></label>

                        <?php if (in_array($key, ["ProfileImage", "AboutImage"], true)): ?>
                            <input type="file" name="<?php echo e($key); ?>" accept="image/*">
                            <?php if (!empty($values[$key])): ?>
                                <div style="margin: 0.8rem 0 1rem;">
                                    <img src="<?php echo e($values[$key]); ?>" alt="Current <?php echo e($key); ?>" style="max-width: 140px; border-radius: 10px; display: block;">
                                    <small class="muted">Choose a new file to replace the image.</small>
                                </div>
                            <?php endif; ?>
                        <?php elseif (in_array($key, ["HomeIntro", "AboutText"], true)): ?>
                            <textarea name="<?php echo e($key); ?>" rows="3"><?php echo e($values[$key]); ?></textarea>
                        <?php else: ?>
                            <input type="text" name="<?php echo e($key); ?>" value="<?php echo e($values[$key]); ?>">
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <button type="submit" class="btn-primary">Save settings</button>
                </form>
            </div>

            <div class="admin-panel">
                <h2>Change password</h2>
                <form method="POST" class="admin-form">
                    <input type="hidden" name="form" value="password">

                    <label>Current password</label>
                    <input type="password" name="current_password" required>

                    <label>New password</label>
                    <input type="password" name="new_password" required minlength="8">

                    <label>Confirm new password</label>
                    <input type="password" name="confirm_password" required minlength="8">

                    <button type="submit" class="btn-primary">Update password</button>
                </form>
            </div>

        </main>

    </div>

</div>

<script src="js/admin.js"></script>

</body>

</html>